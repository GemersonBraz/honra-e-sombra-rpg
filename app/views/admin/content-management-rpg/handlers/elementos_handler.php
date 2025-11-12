<?php
/**
 * Handler para operações CRUD de elementos
 */

// Iniciar output buffering para capturar qualquer saída indesejada
ob_start();

// Desabilitar exibição de erros (retornar apenas JSON)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Buffer de erros
$errors = [];
set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$errors) {
    $errors[] = "$errstr in $errfile on line $errline";
});

try {
    $configPath = dirname(__DIR__, 5) . '/config/db.php';
    if (!file_exists($configPath)) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Arquivo de configuração não encontrado: ' . $configPath
        ]);
        exit;
    }
    
    require_once $configPath;
    
    // Verificar se é admin
    if (!isAdmin()) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acesso negado!']);
        exit;
    }
    
    $conn = getDB();
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            // Criar novo elemento
            $stmt = $conn->prepare("
                INSERT INTO elementos (
                    nome, descricao, cor_hex, icone,
                    forte_contra, fraco_contra, bonus_dano_percentual
                ) VALUES (
                    :nome, :descricao, :cor_hex, :icone,
                    :forte_contra, :fraco_contra, :bonus_dano_percentual
                )
            ");
            
            $stmt->execute([
                'nome' => strtolower(trim($_POST['nome'])),
                'descricao' => trim($_POST['descricao']) ?: null,
                'cor_hex' => trim($_POST['cor_hex']) ?: '#666666',
                'icone' => trim($_POST['icone']) ?: null,
                'forte_contra' => trim($_POST['forte_contra']) ?: null,
                'fraco_contra' => trim($_POST['fraco_contra']) ?: null,
                'bonus_dano_percentual' => (int)($_POST['bonus_dano_percentual'] ?? 50)
            ]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Elemento criado com sucesso!'
            ]);
            exit;
            
        case 'update':
            // Atualizar elemento existente
            $id = (int)$_POST['id'];
            
            $stmt = $conn->prepare("
                UPDATE elementos SET
                    nome = :nome,
                    descricao = :descricao,
                    cor_hex = :cor_hex,
                    icone = :icone,
                    forte_contra = :forte_contra,
                    fraco_contra = :fraco_contra,
                    bonus_dano_percentual = :bonus_dano_percentual
                WHERE id = :id
            ");
            
            $stmt->execute([
                'id' => $id,
                'nome' => strtolower(trim($_POST['nome'])),
                'descricao' => trim($_POST['descricao']) ?: null,
                'cor_hex' => trim($_POST['cor_hex']) ?: '#666666',
                'icone' => trim($_POST['icone']) ?: null,
                'forte_contra' => trim($_POST['forte_contra']) ?: null,
                'fraco_contra' => trim($_POST['fraco_contra']) ?: null,
                'bonus_dano_percentual' => (int)($_POST['bonus_dano_percentual'] ?? 50)
            ]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Elemento atualizado com sucesso!'
            ]);
            exit;
            
        case 'delete':
            // Deletar elemento
            $id = (int)$_POST['id'];
            
            $stmt = $conn->prepare("DELETE FROM elementos WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Elemento excluído com sucesso!'
            ]);
            exit;
            
        default:
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Ação inválida!'
            ]);
            exit;
    }
    
} catch (PDOException $e) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro no banco de dados: ' . $e->getMessage()
    ]);
    exit;
} catch (Exception $e) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage(),
        'errors' => $errors
    ]);
    exit;
}
