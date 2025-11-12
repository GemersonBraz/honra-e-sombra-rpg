<?php
/**
 * Handler para operações CRUD de Classes
 */

// Iniciar output buffering para capturar qualquer saída indesejada
ob_start();

// Desabilitar exibição de erros (retornar apenas JSON)
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
            // Criar nova classe
            $imagem = trim($_POST['imagem']) ?: null;
            
            // Processar upload de imagem
            if (isset($_FILES['imagem_file']) && $_FILES['imagem_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 5) . '/public/img/classes/';
                $fileInfo = pathinfo($_FILES['imagem_file']['name']);
                $extension = strtolower($fileInfo['extension']);
                
                // Validar extensão
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($extension, $allowedExtensions)) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Formato de arquivo não permitido']);
                    exit;
                }
                
                // Validar tamanho (2MB)
                if ($_FILES['imagem_file']['size'] > 2 * 1024 * 1024) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Máximo 2MB']);
                    exit;
                }
                
                // Gerar nome único
                $uniqueName = uniqid('class_', true) . '.' . $extension;
                $uploadPath = $uploadDir . $uniqueName;
                
                // Mover arquivo
                if (move_uploaded_file($_FILES['imagem_file']['tmp_name'], $uploadPath)) {
                    $imagem = 'public/img/classes/' . $uniqueName;
                } else {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload da imagem']);
                    exit;
                }
            }
            
            $stmt = $conn->prepare("
                INSERT INTO classes (
                    nome, descricao, hp_base, atk_base, def_base,
                    elemento_afinidade, especialidade,
                    bonus_especial, imagem, ativo
                ) VALUES (
                    :nome, :descricao, :hp_base, :atk_base, :def_base,
                    :elemento_afinidade, :especialidade,
                    :bonus_especial, :imagem, :ativo
                )
            ");
            
            $stmt->execute([
                'nome' => strtolower(trim($_POST['nome'])),
                'descricao' => trim($_POST['descricao']) ?: null,
                'hp_base' => (int)$_POST['hp_base'],
                'atk_base' => (int)$_POST['atk_base'],
                'def_base' => (int)$_POST['def_base'],
                'elemento_afinidade' => $_POST['elemento_afinidade'] ?: null,
                'especialidade' => trim($_POST['especialidade']) ?: null,
                'bonus_especial' => trim($_POST['bonus_especial']) ?: null,
                'imagem' => $imagem,
                'ativo' => (int)$_POST['ativo']
            ]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Classe criada com sucesso!'
            ]);
            exit;
            
        case 'update':
            // Atualizar classe existente
            $id = (int)$_POST['id'];
            $imagem = trim($_POST['imagem']) ?: null;
            
            // Buscar imagem atual
            $stmt = $conn->prepare("SELECT imagem FROM classes WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $currentImage = $stmt->fetchColumn();
            
            // Processar upload de nova imagem
            if (isset($_FILES['imagem_file']) && $_FILES['imagem_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 5) . '/public/img/classes/';
                $fileInfo = pathinfo($_FILES['imagem_file']['name']);
                $extension = strtolower($fileInfo['extension']);
                
                // Validar extensão
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($extension, $allowedExtensions)) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Formato de arquivo não permitido']);
                    exit;
                }
                
                // Validar tamanho (2MB)
                if ($_FILES['imagem_file']['size'] > 2 * 1024 * 1024) {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Máximo 2MB']);
                    exit;
                }
                
                // Gerar nome único
                $uniqueName = uniqid('class_', true) . '.' . $extension;
                $uploadPath = $uploadDir . $uniqueName;
                
                // Mover arquivo
                if (move_uploaded_file($_FILES['imagem_file']['tmp_name'], $uploadPath)) {
                    // Deletar imagem antiga se existir
                    if ($currentImage && file_exists(dirname(__DIR__, 5) . '/' . $currentImage)) {
                        unlink(dirname(__DIR__, 5) . '/' . $currentImage);
                    }
                    $imagem = 'public/img/classes/' . $uniqueName;
                } else {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload da imagem']);
                    exit;
                }
            } 
            // Verificar se usuário quer remover a imagem
            elseif (isset($_POST['remove_imagem']) && $_POST['remove_imagem'] == '1') {
                // Deletar imagem atual se existir
                if ($currentImage && file_exists(dirname(__DIR__, 5) . '/' . $currentImage)) {
                    unlink(dirname(__DIR__, 5) . '/' . $currentImage);
                }
                $imagem = null; // Remover referência da imagem no banco
            }
            else {
                // Manter imagem atual se não houver upload nem remoção
                $imagem = $currentImage;
            }
            
            $stmt = $conn->prepare("
                UPDATE classes SET
                    nome = :nome,
                    descricao = :descricao,
                    hp_base = :hp_base,
                    atk_base = :atk_base,
                    def_base = :def_base,
                    elemento_afinidade = :elemento_afinidade,
                    especialidade = :especialidade,
                    bonus_especial = :bonus_especial,
                    imagem = :imagem,
                    ativo = :ativo
                WHERE id = :id
            ");
            
            $stmt->execute([
                'id' => $id,
                'nome' => strtolower(trim($_POST['nome'])),
                'descricao' => trim($_POST['descricao']) ?: null,
                'hp_base' => (int)$_POST['hp_base'],
                'atk_base' => (int)$_POST['atk_base'],
                'def_base' => (int)$_POST['def_base'],
                'elemento_afinidade' => $_POST['elemento_afinidade'] ?: null,
                'especialidade' => trim($_POST['especialidade']) ?: null,
                'bonus_especial' => trim($_POST['bonus_especial']) ?: null,
                'imagem' => $imagem,
                'ativo' => (int)$_POST['ativo']
            ]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Classe atualizada com sucesso!'
            ]);
            exit;
            
        case 'delete':
            // Deletar classe
            $id = (int)$_POST['id'];
            
            // Verificar se há personagens usando esta classe
            $stmt = $conn->prepare("SELECT COUNT(*) FROM characters WHERE classe = (SELECT nome FROM classes WHERE id = :id)");
            $stmt->execute(['id' => $id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => "Não é possível excluir! Existem {$count} personagens usando esta classe."
                ]);
                exit;
            }
            
            // Buscar e deletar imagem associada
            $stmt = $conn->prepare("SELECT imagem FROM classes WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $imagem = $stmt->fetchColumn();
            
            if ($imagem && file_exists(dirname(__DIR__, 5) . '/' . $imagem)) {
                unlink(dirname(__DIR__, 5) . '/' . $imagem);
            }
            
            $stmt = $conn->prepare("DELETE FROM classes WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Classe excluída com sucesso!'
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
