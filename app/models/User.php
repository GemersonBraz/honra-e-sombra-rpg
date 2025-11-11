<?php
/**
 * Model User - Sistema Honra e Sombra RPG
 */

require_once __DIR__ . '/../../config/db.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Atualiza o caminho do avatar do usuário
     * @param int $userId
     * @param string $avatarPath Caminho relativo dentro de /public (ex.: img/avatars/uploads/xxx.png)
     * @return array {success: bool, message?: string}
     */
    public function updateAvatar(int $userId, string $avatarPath): array {
        try {
            $stmt = $this->db->prepare("UPDATE users SET avatar = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND ativo = 1");
            $ok = $stmt->execute([$avatarPath, $userId]);
            if ($ok) {
                // Atualiza sessão para refletir imediatamente
                $_SESSION['user_avatar'] = $avatarPath;
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Não foi possível atualizar o avatar'];
        } catch (Exception $e) {
            // Provavelmente a coluna 'avatar' não existe ainda
            return ['success' => false, 'message' => 'Falha ao atualizar avatar (execute a migração para adicionar a coluna avatar).'];
        }
    }
    
    /**
     * Criar novo usuário
     */
    public function create($nome, $email, $senha, $tipo = 'player') {
        try {
            // Verificar se email já existe
            if ($this->emailExists($email)) {
                return ['success' => false, 'message' => 'Email já cadastrado'];
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO users (nome, email, senha, tipo) 
                VALUES (?, ?, ?, ?)
            ");
            
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $result = $stmt->execute([$nome, $email, $senhaHash, $tipo]);
            
            if ($result) {
                return ['success' => true, 'id' => $this->db->lastInsertId()];
            }
            
            return ['success' => false, 'message' => 'Erro ao criar usuário'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()];
        }
    }
    
    /**
     * Autenticar usuário
     */
    public function login($email, $senha) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, nome, email, senha, tipo 
                FROM users 
                WHERE email = ? AND ativo = 1
            ");
            
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($senha, $user['senha'])) {
                // Atualizar último login
                $this->updateLastLogin($user['id']);
                
                // Criar sessão
                // Regera o ID de sessão para mitigar fixation após login bem-sucedido
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nome'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_tipo'] = $user['tipo'];
                $_SESSION['login_time'] = time();
                
                return ['success' => true, 'user' => $user];
            }
            
            return ['success' => false, 'message' => 'Email ou senha inválidos'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()];
        }
    }
    
    /**
     * Logout do usuário
     */
    public function logout() {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Buscar usuário por ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, tipo, data_criacao, ultimo_login 
            FROM users 
            WHERE id = ? AND ativo = 1
        ");
        
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obter informações completas do usuário
     */
    public function getUserInfo($id) {
        $stmt = $this->db->prepare("
            SELECT id, nome, email, tipo, data_criacao, ultimo_login,
                   DATE_FORMAT(data_criacao, '%Y-%m-%d %H:%i:%s') as data_criacao,
                   DATE_FORMAT(ultimo_login, '%Y-%m-%d %H:%i:%s') as ultimo_acesso
            FROM users 
            WHERE id = ? AND ativo = 1
        ");
        
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if ($user) {
            return [
                'id' => $user['id'],
                'nome' => $user['nome'],
                'email' => $user['email'],
                'tipo' => $user['tipo'],
                'data_criacao' => $user['data_criacao'],
                'ultimo_acesso' => $user['ultimo_acesso'] ?? date('Y-m-d H:i:s')
            ];
        }
        
        return null;
    }
    
    /**
     * Verificar se email já existe
     */
    private function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Atualizar último login
     */
    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET ultimo_login = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
    }
    
    /**
     * Validar dados de entrada
     */
    public function validate($data, $isLogin = false) {
        $errors = [];
        
        if (!$isLogin) {
            if (empty($data['nome']) || strlen($data['nome']) < 2) {
                $errors['nome'] = 'Nome deve ter pelo menos 2 caracteres';
            } elseif (strlen($data['nome']) > 100) {
                $errors['nome'] = 'Nome deve ter no máximo 100 caracteres';
            }
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } elseif (strlen($data['email']) > 191) {
            $errors['email'] = 'Email deve ter no máximo 191 caracteres';
        }
        
        if (empty($data['senha']) || strlen($data['senha']) < 6) {
            $errors['senha'] = 'Senha deve ter pelo menos 6 caracteres';
        }
        
        if (!$isLogin && isset($data['confirma_senha'])) {
            if ($data['senha'] !== $data['confirma_senha']) {
                $errors['confirma_senha'] = 'Senhas não conferem';
            }
        }
        
        return $errors;
    }
}
?>