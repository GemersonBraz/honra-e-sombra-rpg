<?php

class Character {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Criar novo personagem
     */
    public function createCharacter($userId, $data) {
        // Validar limites do jogador
        if (!$this->canCreateCharacter($userId)) {
            return ['success' => false, 'message' => 'Você atingiu o limite máximo de personagens!'];
        }
        
        // Pegar configurações iniciais
        $config = new GameConfig();
        $pontosIniciais = $config->get('character_pontos_iniciais', 3);
        $dinheiroInicial = $config->get('character_dinheiro_inicial', 100);
        $hpInicial = $config->get('character_hp_inicial', 100);
        
        // Validar dados obrigatórios
        $required = ['nome', 'classe', 'sexo', 'elemento_principal'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Campo obrigatório: $field"];
            }
        }
        
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO characters (
                    user_id, nome, classe, nivel, sexo, avatar,
                    hp_atual, hp_maximo, elemento_principal, elemento_extra,
                    xp, pontos_habilidade, dinheiro,
                    historia, personalidade, objetivos, status, ativo
                ) VALUES (
                    :user_id, :nome, :classe, 'principiante', :sexo, :avatar,
                    :hp_inicial, :hp_inicial, :elemento_principal, :elemento_extra,
                    0, :pontos_habilidade, :dinheiro_inicial,
                    :historia, :personalidade, :objetivos, 'ativo', 1
                )
            ");
            
            $stmt->execute([
                'user_id' => $userId,
                'nome' => $data['nome'],
                'classe' => $data['classe'],
                'sexo' => $data['sexo'],
                'avatar' => $data['avatar'] ?? null,
                'hp_inicial' => $hpInicial,
                'elemento_principal' => $data['elemento_principal'],
                'elemento_extra' => $data['elemento_extra'] ?? null,
                'pontos_habilidade' => $pontosIniciais,
                'dinheiro_inicial' => $dinheiroInicial,
                'historia' => $data['historia'] ?? null,
                'personalidade' => $data['personalidade'] ?? null,
                'objetivos' => $data['objetivos'] ?? null
            ]);
            
            $characterId = $this->conn->lastInsertId();
            
            return [
                'success' => true,
                'character_id' => $characterId,
                'message' => 'Personagem criado com sucesso!',
                'data' => [
                    'pontos_habilidade' => $pontosIniciais,
                    'dinheiro' => $dinheiroInicial,
                    'hp' => $hpInicial
                ]
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao criar personagem: ' . $e->getMessage()];
        }
    }
    
    /**
     * Verificar se jogador pode criar mais personagens
     */
    public function canCreateCharacter($userId) {
        $config = new GameConfig();
        $maxPersonagens = $config->get('character_max_por_jogador', 5);
        
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM characters 
            WHERE user_id = :user_id AND ativo = 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] < $maxPersonagens;
    }
    
    /**
     * Listar personagens do jogador
     */
    public function getUserCharacters($userId) {
        $stmt = $this->conn->prepare("
            SELECT 
                c.*,
                (SELECT COUNT(*) FROM character_habilidades WHERE character_id = c.id) as total_habilidades,
                (SELECT COUNT(*) FROM character_golpes_especiais WHERE character_id = c.id) as total_golpes
            FROM characters c
            WHERE c.user_id = :user_id AND c.ativo = 1
            ORDER BY c.data_criacao DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Pegar detalhes completos do personagem
     */
    public function getCharacterDetails($characterId, $userId = null) {
        $sql = "
            SELECT c.*,
                u.nome as jogador_nome,
                u.email as jogador_email
            FROM characters c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.id = :character_id
        ";
        
        if ($userId) {
            $sql .= " AND c.user_id = :user_id";
        }
        
        $stmt = $this->conn->prepare($sql);
        $params = ['character_id' => $characterId];
        if ($userId) {
            $params['user_id'] = $userId;
        }
        $stmt->execute($params);
        
        $character = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$character) {
            return null;
        }
        
        // Buscar habilidades
        $character['habilidades'] = $this->getCharacterHabilidades($characterId);
        
        // Buscar golpes especiais
        $character['golpes_especiais'] = $this->getCharacterGolpes($characterId);
        
        // Buscar inventário
        $character['inventario'] = $this->getCharacterInventario($characterId);
        
        // Buscar penalidades ativas
        $character['penalidades'] = $this->getCharacterPenalidades($characterId);
        
        return $character;
    }
    
    /**
     * Adicionar habilidade ao personagem
     */
    public function addHabilidade($characterId, $habilidadeData) {
        // Verificar se tem pontos disponíveis
        $character = $this->getCharacterDetails($characterId);
        if ($character['pontos_habilidade'] <= 0) {
            return ['success' => false, 'message' => 'Sem pontos de habilidade disponíveis!'];
        }
        
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO character_habilidades (
                    character_id, categoria, tipo, nome, descricao,
                    bonus_atk, bonus_def, bonus_hp, nivel_requerido
                ) VALUES (
                    :character_id, :categoria, :tipo, :nome, :descricao,
                    :bonus_atk, :bonus_def, :bonus_hp, :nivel_requerido
                )
            ");
            
            $stmt->execute([
                'character_id' => $characterId,
                'categoria' => $habilidadeData['categoria'],
                'tipo' => $habilidadeData['tipo'],
                'nome' => $habilidadeData['nome'],
                'descricao' => $habilidadeData['descricao'] ?? null,
                'bonus_atk' => $habilidadeData['bonus_atk'] ?? 0,
                'bonus_def' => $habilidadeData['bonus_def'] ?? 0,
                'bonus_hp' => $habilidadeData['bonus_hp'] ?? 0,
                'nivel_requerido' => $character['nivel']
            ]);
            
            // Deduzir 1 ponto de habilidade
            $this->conn->prepare("
                UPDATE characters 
                SET pontos_habilidade = pontos_habilidade - 1 
                WHERE id = :id
            ")->execute(['id' => $characterId]);
            
            return ['success' => true, 'message' => 'Habilidade adicionada!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao adicionar habilidade: ' . $e->getMessage()];
        }
    }
    
    /**
     * Atualizar HP do personagem
     */
    public function updateHP($characterId, $novoHP, $motivo = null) {
        $stmt = $this->conn->prepare("
            UPDATE characters 
            SET hp_atual = :hp_atual,
                status = CASE 
                    WHEN :hp_atual <= 0 THEN 'coma'
                    WHEN :hp_atual <= 20 THEN 'ferido'
                    ELSE 'ativo'
                END
            WHERE id = :id
        ");
        
        $stmt->execute([
            'hp_atual' => max(0, $novoHP),
            'id' => $characterId
        ]);
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Adicionar XP e verificar level up
     */
    public function addXP($characterId, $xp) {
        $character = $this->getCharacterDetails($characterId);
        $novoXP = $character['xp'] + $xp;
        
        $config = new GameConfig();
        $niveis = [
            'principiante' => ['xp' => 0, 'proximo' => 'experiente'],
            'experiente' => ['xp' => $config->get('xp_por_nivel_experiente', 1000), 'proximo' => 'veterano'],
            'veterano' => ['xp' => $config->get('xp_por_nivel_veterano', 3000), 'proximo' => 'mestre'],
            'mestre' => ['xp' => $config->get('xp_por_nivel_mestre', 6000), 'proximo' => null]
        ];
        
        $nivelAtual = $character['nivel'];
        $levelUp = false;
        
        // Verificar se subiu de nível
        if ($niveis[$nivelAtual]['proximo']) {
            $xpNecessario = $niveis[$niveis[$nivelAtual]['proximo']]['xp'];
            if ($novoXP >= $xpNecessario) {
                $nivelAtual = $niveis[$nivelAtual]['proximo'];
                $levelUp = true;
                
                // Ganhar pontos de habilidade
                $pontosGanhos = $config->get('pontos_habilidade_por_nivel', 2);
                $this->conn->prepare("
                    UPDATE characters 
                    SET pontos_habilidade = pontos_habilidade + :pontos
                    WHERE id = :id
                ")->execute(['pontos' => $pontosGanhos, 'id' => $characterId]);
            }
        }
        
        // Atualizar XP e nível
        $stmt = $this->conn->prepare("
            UPDATE characters 
            SET xp = :xp, nivel = :nivel 
            WHERE id = :id
        ");
        $stmt->execute([
            'xp' => $novoXP,
            'nivel' => $nivelAtual,
            'id' => $characterId
        ]);
        
        return [
            'success' => true,
            'xp_ganho' => $xp,
            'xp_total' => $novoXP,
            'level_up' => $levelUp,
            'nivel_atual' => $nivelAtual
        ];
    }
    
    /**
     * Pegar habilidades do personagem
     */
    private function getCharacterHabilidades($characterId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM character_habilidades 
            WHERE character_id = :character_id AND ativo = 1
            ORDER BY data_adquirida DESC
        ");
        $stmt->execute(['character_id' => $characterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Pegar golpes especiais do personagem
     */
    private function getCharacterGolpes($characterId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM character_golpes_especiais 
            WHERE character_id = :character_id AND ativo = 1
            ORDER BY nivel_criacao, nome
        ");
        $stmt->execute(['character_id' => $characterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Pegar inventário do personagem
     */
    private function getCharacterInventario($characterId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM character_inventario 
            WHERE character_id = :character_id
            ORDER BY equipado DESC, tipo, nome_item
        ");
        $stmt->execute(['character_id' => $characterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Pegar penalidades ativas do personagem
     */
    private function getCharacterPenalidades($characterId) {
        $stmt = $this->conn->prepare("
            SELECT * FROM character_penalidades 
            WHERE character_id = :character_id AND ativo = 1
            ORDER BY data_inicio DESC
        ");
        $stmt->execute(['character_id' => $characterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Deletar personagem (soft delete)
     */
    public function deleteCharacter($characterId, $userId) {
        $stmt = $this->conn->prepare("
            UPDATE characters 
            SET ativo = 0, status = 'morto' 
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->execute(['id' => $characterId, 'user_id' => $userId]);
        
        return $stmt->rowCount() > 0;
    }
}
