<?php

class GameConfig {
    private $conn;
    private static $cache = [];
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Pegar valor de configuração
     */
    public function get($chave, $default = null) {
        // Verificar cache primeiro
        if (isset(self::$cache[$chave])) {
            return self::$cache[$chave];
        }
        
        try {
            $stmt = $this->conn->prepare("
                SELECT valor, tipo 
                FROM game_config 
                WHERE chave = :chave
            ");
            $stmt->execute(['chave' => $chave]);
            $config = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$config) {
                return $default;
            }
            
            // Converter valor baseado no tipo
            $valor = $this->convertValue($config['valor'], $config['tipo']);
            
            // Armazenar em cache
            self::$cache[$chave] = $valor;
            
            return $valor;
            
        } catch (PDOException $e) {
            return $default;
        }
    }
    
    /**
     * Setar valor de configuração (apenas admin)
     */
    public function set($chave, $valor, $tipo = null) {
        try {
            // Se tipo não foi especificado, pegar do banco
            if (!$tipo) {
                $stmt = $this->conn->prepare("SELECT tipo FROM game_config WHERE chave = :chave");
                $stmt->execute(['chave' => $chave]);
                $config = $stmt->fetch(PDO::FETCH_ASSOC);
                $tipo = $config['tipo'] ?? 'texto';
            }
            
            // Validar tipo
            $valorFormatado = $this->formatValue($valor, $tipo);
            
            $stmt = $this->conn->prepare("
                UPDATE game_config 
                SET valor = :valor 
                WHERE chave = :chave AND editavel_admin = 1
            ");
            
            $stmt->execute([
                'valor' => $valorFormatado,
                'chave' => $chave
            ]);
            
            // Limpar cache
            unset(self::$cache[$chave]);
            
            return [
                'success' => $stmt->rowCount() > 0,
                'message' => $stmt->rowCount() > 0 ? 'Configuração atualizada!' : 'Configuração não encontrada ou não editável.'
            ];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Pegar todas as configurações de uma categoria
     */
    public function getByCategory($categoria) {
        $stmt = $this->conn->prepare("
            SELECT chave, valor, tipo, descricao, editavel_admin 
            FROM game_config 
            WHERE categoria = :categoria
            ORDER BY chave
        ");
        $stmt->execute(['categoria' => $categoria]);
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($configs as $config) {
            $result[$config['chave']] = [
                'valor' => $this->convertValue($config['valor'], $config['tipo']),
                'tipo' => $config['tipo'],
                'descricao' => $config['descricao'],
                'editavel' => (bool)$config['editavel_admin']
            ];
        }
        
        return $result;
    }
    
    /**
     * Pegar todas as categorias disponíveis
     */
    public function getAllCategories() {
        $stmt = $this->conn->query("
            SELECT DISTINCT categoria 
            FROM game_config 
            ORDER BY categoria
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Pegar todas as configurações
     */
    public function getAll() {
        $stmt = $this->conn->query("
            SELECT chave, valor, tipo, categoria, descricao, editavel_admin 
            FROM game_config 
            ORDER BY categoria, chave
        ");
        $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($configs as $config) {
            if (!isset($result[$config['categoria']])) {
                $result[$config['categoria']] = [];
            }
            
            $result[$config['categoria']][$config['chave']] = [
                'valor' => $this->convertValue($config['valor'], $config['tipo']),
                'tipo' => $config['tipo'],
                'descricao' => $config['descricao'],
                'editavel' => (bool)$config['editavel_admin']
            ];
        }
        
        return $result;
    }
    
    /**
     * Atualizar múltiplas configurações de uma vez
     */
    public function updateMultiple($configs) {
        $success = 0;
        $errors = [];
        
        foreach ($configs as $chave => $valor) {
            $result = $this->set($chave, $valor);
            if ($result['success']) {
                $success++;
            } else {
                $errors[$chave] = $result['message'];
            }
        }
        
        return [
            'success' => $success > 0,
            'updated' => $success,
            'errors' => $errors
        ];
    }
    
    /**
     * Resetar configuração para valor padrão
     */
    public function reset($chave) {
        // Esta função pode ser expandida para ter valores padrão em outro lugar
        // Por enquanto, apenas retorna erro
        return ['success' => false, 'message' => 'Reset não implementado. Defina o valor manualmente.'];
    }
    
    /**
     * Criar nova configuração (apenas para expansão futura)
     */
    public function create($chave, $valor, $tipo, $categoria, $descricao = '', $editavel = true) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO game_config (chave, valor, tipo, categoria, descricao, editavel_admin)
                VALUES (:chave, :valor, :tipo, :categoria, :descricao, :editavel)
            ");
            
            $stmt->execute([
                'chave' => $chave,
                'valor' => $this->formatValue($valor, $tipo),
                'tipo' => $tipo,
                'categoria' => $categoria,
                'descricao' => $descricao,
                'editavel' => $editavel ? 1 : 0
            ]);
            
            return ['success' => true, 'message' => 'Configuração criada!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro ao criar: ' . $e->getMessage()];
        }
    }
    
    /**
     * Converter valor do banco para tipo correto
     */
    private function convertValue($valor, $tipo) {
        switch ($tipo) {
            case 'inteiro':
                return (int)$valor;
            case 'decimal':
                return (float)$valor;
            case 'booleano':
                return $valor === '1' || $valor === 'true';
            case 'json':
                return json_decode($valor, true);
            case 'texto':
            default:
                return $valor;
        }
    }
    
    /**
     * Formatar valor para salvar no banco
     */
    private function formatValue($valor, $tipo) {
        switch ($tipo) {
            case 'inteiro':
                return (string)(int)$valor;
            case 'decimal':
                return (string)(float)$valor;
            case 'booleano':
                return $valor ? '1' : '0';
            case 'json':
                return is_string($valor) ? $valor : json_encode($valor);
            case 'texto':
            default:
                return (string)$valor;
        }
    }
    
    /**
     * Validar tipo de valor
     */
    public function validateType($valor, $tipo) {
        switch ($tipo) {
            case 'inteiro':
                return is_numeric($valor) && (int)$valor == $valor;
            case 'decimal':
                return is_numeric($valor);
            case 'booleano':
                return is_bool($valor) || in_array($valor, ['0', '1', 'true', 'false', true, false]);
            case 'json':
                if (is_array($valor)) return true;
                json_decode($valor);
                return json_last_error() === JSON_ERROR_NONE;
            case 'texto':
                return is_string($valor) || is_numeric($valor);
            default:
                return true;
        }
    }
    
    /**
     * Limpar todo o cache
     */
    public static function clearCache() {
        self::$cache = [];
    }
}
