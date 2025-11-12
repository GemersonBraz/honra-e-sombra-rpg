<?php

class Shop {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Listar itens disponíveis na loja
     */
    public function getAvailableItems($filters = []) {
        $sql = "SELECT * FROM vw_loja_disponivel WHERE 1=1";
        $params = [];
        
        // Filtrar por tipo
        if (!empty($filters['tipo'])) {
            $sql .= " AND tipo = :tipo";
            $params['tipo'] = $filters['tipo'];
        }
        
        // Filtrar por raridade
        if (!empty($filters['raridade'])) {
            $sql .= " AND raridade = :raridade";
            $params['raridade'] = $filters['raridade'];
        }
        
        // Filtrar por classe (verificar se personagem pode usar)
        if (!empty($filters['classe'])) {
            $sql .= " AND (classe_permitida IS NULL OR classe_permitida LIKE :classe)";
            $params['classe'] = "%{$filters['classe']}%";
        }
        
        // Filtrar por nível mínimo
        if (!empty($filters['nivel'])) {
            $niveis = ['principiante' => 1, 'experiente' => 2, 'veterano' => 3, 'mestre' => 4];
            $nivelNum = $niveis[$filters['nivel']] ?? 1;
            $sql .= " AND (nivel_minimo IS NULL OR 
                CASE nivel_minimo
                    WHEN 'principiante' THEN 1
                    WHEN 'experiente' THEN 2
                    WHEN 'veterano' THEN 3
                    WHEN 'mestre' THEN 4
                END <= :nivel)";
            $params['nivel'] = $nivelNum;
        }
        
        // Ordenar
        $orderBy = $filters['ordenar'] ?? 'nome';
        $validOrders = ['nome', 'preco_compra', 'raridade', 'tipo'];
        if (!in_array($orderBy, $validOrders)) {
            $orderBy = 'nome';
        }
        $sql .= " ORDER BY $orderBy";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Comprar item da loja
     */
    public function purchaseItem($characterId, $itemId, $quantidade = 1) {
        // Buscar dados do personagem
        $character = $this->getCharacter($characterId);
        if (!$character) {
            return ['success' => false, 'message' => 'Personagem não encontrado!'];
        }
        
        // Buscar item
        $item = $this->getItem($itemId);
        if (!$item || !$item['disponivel']) {
            return ['success' => false, 'message' => 'Item não disponível!'];
        }
        
        // Verificar estoque
        if (!$item['estoque_infinito'] && $item['estoque_atual'] < $quantidade) {
            return ['success' => false, 'message' => 'Estoque insuficiente!'];
        }
        
        // Calcular preço total
        $precoTotal = $item['preco_compra'] * $quantidade;
        
        // Verificar dinheiro
        if ($character['dinheiro'] < $precoTotal) {
            return ['success' => false, 'message' => 'Dinheiro insuficiente!'];
        }
        
        // Verificar restrições de classe
        if ($item['classe_permitida']) {
            $classesPermitidas = explode(',', $item['classe_permitida']);
            if (!in_array($character['classe'], $classesPermitidas)) {
                return ['success' => false, 'message' => 'Sua classe não pode usar este item!'];
            }
        }
        
        // Verificar nível mínimo
        if ($item['nivel_minimo']) {
            $niveis = ['principiante' => 1, 'experiente' => 2, 'veterano' => 3, 'mestre' => 4];
            $nivelChar = $niveis[$character['nivel']];
            $nivelItem = $niveis[$item['nivel_minimo']];
            
            if ($nivelChar < $nivelItem) {
                return ['success' => false, 'message' => 'Nível insuficiente para este item!'];
            }
        }
        
        // Verificar habilidade requerida
        if ($item['requer_habilidade']) {
            if (!$this->characterHasSkill($characterId, $item['requer_habilidade'])) {
                return ['success' => false, 'message' => "Requer habilidade: {$item['requer_habilidade']}"];
            }
        }
        
        try {
            $this->conn->beginTransaction();
            
            // Deduzir dinheiro do personagem
            $novoDinheiro = $character['dinheiro'] - $precoTotal;
            $stmt = $this->conn->prepare("
                UPDATE characters 
                SET dinheiro = :dinheiro 
                WHERE id = :id
            ");
            $stmt->execute(['dinheiro' => $novoDinheiro, 'id' => $characterId]);
            
            // Atualizar estoque se não for infinito
            if (!$item['estoque_infinito']) {
                $stmt = $this->conn->prepare("
                    UPDATE loja_itens 
                    SET estoque_atual = estoque_atual - :quantidade 
                    WHERE id = :id
                ");
                $stmt->execute(['quantidade' => $quantidade, 'id' => $itemId]);
            }
            
            // Adicionar ao inventário do personagem
            $this->addToInventory($characterId, $item, $quantidade);
            
            // Registrar transação
            $stmt = $this->conn->prepare("
                INSERT INTO loja_transacoes (
                    user_id, character_id, item_id, tipo_transacao,
                    quantidade, preco_unitario, preco_total,
                    dinheiro_antes, dinheiro_depois
                ) VALUES (
                    :user_id, :character_id, :item_id, 'compra',
                    :quantidade, :preco_unitario, :preco_total,
                    :dinheiro_antes, :dinheiro_depois
                )
            ");
            
            $stmt->execute([
                'user_id' => $character['user_id'],
                'character_id' => $characterId,
                'item_id' => $itemId,
                'quantidade' => $quantidade,
                'preco_unitario' => $item['preco_compra'],
                'preco_total' => $precoTotal,
                'dinheiro_antes' => $character['dinheiro'],
                'dinheiro_depois' => $novoDinheiro
            ]);
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => "Você comprou {$quantidade}x {$item['nome']}!",
                'dinheiro_restante' => $novoDinheiro,
                'item' => $item['nome']
            ];
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Erro na compra: ' . $e->getMessage()];
        }
    }
    
    /**
     * Vender item do inventário
     */
    public function sellItem($characterId, $inventarioItemId, $quantidade = 1) {
        // Buscar item do inventário
        $stmt = $this->conn->prepare("
            SELECT * FROM character_inventario 
            WHERE id = :id AND character_id = :character_id
        ");
        $stmt->execute(['id' => $inventarioItemId, 'character_id' => $characterId]);
        $invItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$invItem) {
            return ['success' => false, 'message' => 'Item não encontrado no inventário!'];
        }
        
        if ($invItem['quantidade'] < $quantidade) {
            return ['success' => false, 'message' => 'Quantidade insuficiente!'];
        }
        
        if ($invItem['equipado']) {
            return ['success' => false, 'message' => 'Desequipe o item antes de vender!'];
        }
        
        // Buscar dados do personagem
        $character = $this->getCharacter($characterId);
        
        // Buscar item da loja para pegar preço de venda
        $item = $this->getItem($invItem['item_id']);
        
        // Calcular preço de venda (usar taxa configurada)
        $config = new GameConfig();
        $taxaVenda = $config->get('loja_taxa_venda', 0.5);
        $precoVenda = $item ? floor($item['preco_compra'] * $taxaVenda) : 1;
        $precoTotal = $precoVenda * $quantidade;
        
        try {
            $this->conn->beginTransaction();
            
            // Adicionar dinheiro ao personagem
            $novoDinheiro = $character['dinheiro'] + $precoTotal;
            $stmt = $this->conn->prepare("
                UPDATE characters 
                SET dinheiro = :dinheiro 
                WHERE id = :id
            ");
            $stmt->execute(['dinheiro' => $novoDinheiro, 'id' => $characterId]);
            
            // Remover do inventário
            if ($invItem['quantidade'] == $quantidade) {
                // Deletar item
                $stmt = $this->conn->prepare("DELETE FROM character_inventario WHERE id = :id");
                $stmt->execute(['id' => $inventarioItemId]);
            } else {
                // Diminuir quantidade
                $stmt = $this->conn->prepare("
                    UPDATE character_inventario 
                    SET quantidade = quantidade - :quantidade 
                    WHERE id = :id
                ");
                $stmt->execute(['quantidade' => $quantidade, 'id' => $inventarioItemId]);
            }
            
            // Registrar transação
            $stmt = $this->conn->prepare("
                INSERT INTO loja_transacoes (
                    user_id, character_id, item_id, tipo_transacao,
                    quantidade, preco_unitario, preco_total,
                    dinheiro_antes, dinheiro_depois
                ) VALUES (
                    :user_id, :character_id, :item_id, 'venda',
                    :quantidade, :preco_unitario, :preco_total,
                    :dinheiro_antes, :dinheiro_depois
                )
            ");
            
            $stmt->execute([
                'user_id' => $character['user_id'],
                'character_id' => $characterId,
                'item_id' => $invItem['item_id'],
                'quantidade' => $quantidade,
                'preco_unitario' => $precoVenda,
                'preco_total' => $precoTotal,
                'dinheiro_antes' => $character['dinheiro'],
                'dinheiro_depois' => $novoDinheiro
            ]);
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => "Você vendeu {$quantidade}x {$invItem['nome_item']} por {$precoTotal} moedas!",
                'dinheiro_total' => $novoDinheiro
            ];
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Erro na venda: ' . $e->getMessage()];
        }
    }
    
    /**
     * Admin: Adicionar novo item à loja
     */
    public function addItem($data, $adminUserId) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO loja_itens (
                    nome, tipo, subtipo, descricao,
                    preco_compra, preco_venda,
                    atk_bonus, def_bonus, hp_bonus,
                    classe_permitida, nivel_minimo, requer_habilidade,
                    estoque_infinito, estoque_atual, raridade,
                    efeitos, imagem, criado_por_admin
                ) VALUES (
                    :nome, :tipo, :subtipo, :descricao,
                    :preco_compra, :preco_venda,
                    :atk_bonus, :def_bonus, :hp_bonus,
                    :classe_permitida, :nivel_minimo, :requer_habilidade,
                    :estoque_infinito, :estoque_atual, :raridade,
                    :efeitos, :imagem, :admin_id
                )
            ");
            
            $stmt->execute([
                'nome' => $data['nome'],
                'tipo' => $data['tipo'],
                'subtipo' => $data['subtipo'] ?? null,
                'descricao' => $data['descricao'] ?? null,
                'preco_compra' => $data['preco_compra'],
                'preco_venda' => $data['preco_venda'] ?? floor($data['preco_compra'] * 0.5),
                'atk_bonus' => $data['atk_bonus'] ?? 0,
                'def_bonus' => $data['def_bonus'] ?? 0,
                'hp_bonus' => $data['hp_bonus'] ?? 0,
                'classe_permitida' => $data['classe_permitida'] ?? null,
                'nivel_minimo' => $data['nivel_minimo'] ?? null,
                'requer_habilidade' => $data['requer_habilidade'] ?? null,
                'estoque_infinito' => $data['estoque_infinito'] ?? 1,
                'estoque_atual' => $data['estoque_atual'] ?? null,
                'raridade' => $data['raridade'] ?? 'comum',
                'efeitos' => $data['efeitos'] ?? null,
                'imagem' => $data['imagem'] ?? null,
                'admin_id' => $adminUserId
            ]);
            
            return [
                'success' => true,
                'item_id' => $this->conn->lastInsertId(),
                'message' => 'Item adicionado à loja!'
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao adicionar item: ' . $e->getMessage()];
        }
    }
    
    /**
     * Admin: Atualizar item da loja
     */
    public function updateItem($itemId, $data) {
        $fields = [];
        $params = ['id' => $itemId];
        
        $allowedFields = [
            'nome', 'tipo', 'subtipo', 'descricao',
            'preco_compra', 'preco_venda',
            'atk_bonus', 'def_bonus', 'hp_bonus',
            'classe_permitida', 'nivel_minimo', 'requer_habilidade',
            'estoque_infinito', 'estoque_atual', 'raridade',
            'efeitos', 'imagem', 'disponivel'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return ['success' => false, 'message' => 'Nenhum campo para atualizar!'];
        }
        
        try {
            $sql = "UPDATE loja_itens SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Item atualizado!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Admin: Deletar item da loja
     */
    public function deleteItem($itemId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM loja_itens WHERE id = :id");
            $stmt->execute(['id' => $itemId]);
            
            return ['success' => true, 'message' => 'Item removido da loja!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao remover: ' . $e->getMessage()];
        }
    }
    
    /**
     * Pegar histórico de transações
     */
    public function getTransactions($characterId = null, $limit = 50) {
        $sql = "
            SELECT t.*, l.nome as item_nome, l.tipo as item_tipo
            FROM loja_transacoes t
            LEFT JOIN loja_itens l ON t.item_id = l.id
        ";
        
        if ($characterId) {
            $sql .= " WHERE t.character_id = :character_id";
        }
        
        $sql .= " ORDER BY t.data_transacao DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        if ($characterId) {
            $stmt->bindValue('character_id', $characterId, PDO::PARAM_INT);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Métodos auxiliares privados
    
    private function getCharacter($characterId) {
        $stmt = $this->conn->prepare("SELECT * FROM characters WHERE id = :id");
        $stmt->execute(['id' => $characterId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getItem($itemId) {
        $stmt = $this->conn->prepare("SELECT * FROM loja_itens WHERE id = :id");
        $stmt->execute(['id' => $itemId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function characterHasSkill($characterId, $skillName) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM character_habilidades 
            WHERE character_id = :character_id 
            AND (nome LIKE :skill OR tipo LIKE :skill)
            AND ativo = 1
        ");
        $stmt->execute(['character_id' => $characterId, 'skill' => "%$skillName%"]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }
    
    private function addToInventory($characterId, $item, $quantidade) {
        // Verificar se já existe no inventário
        $stmt = $this->conn->prepare("
            SELECT id, quantidade 
            FROM character_inventario 
            WHERE character_id = :character_id AND item_id = :item_id
        ");
        $stmt->execute(['character_id' => $characterId, 'item_id' => $item['id']]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Aumentar quantidade
            $stmt = $this->conn->prepare("
                UPDATE character_inventario 
                SET quantidade = quantidade + :quantidade 
                WHERE id = :id
            ");
            $stmt->execute(['quantidade' => $quantidade, 'id' => $existing['id']]);
        } else {
            // Inserir novo item
            $stmt = $this->conn->prepare("
                INSERT INTO character_inventario (
                    character_id, tipo, item_id, nome_item, quantidade, descricao
                ) VALUES (
                    :character_id, :tipo, :item_id, :nome_item, :quantidade, :descricao
                )
            ");
            $stmt->execute([
                'character_id' => $characterId,
                'tipo' => $item['tipo'],
                'item_id' => $item['id'],
                'nome_item' => $item['nome'],
                'quantidade' => $quantidade,
                'descricao' => $item['descricao']
            ]);
        }
    }
}
