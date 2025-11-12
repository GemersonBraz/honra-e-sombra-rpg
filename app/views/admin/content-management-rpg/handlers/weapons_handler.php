<?php
/**
 * Handler para operações CRUD de Armas
 */

// Iniciar output buffering
ob_start();

// Desabilitar exibição de erros (retornar apenas JSON)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Buffer de erros
$errors = [];
set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$errors) {
    $errors[] = $errstr;
    return true;
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
} catch (Exception $e) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar configuração: ' . $e->getMessage()
    ]);
    exit;
}

// Conectar ao banco
$conn = getDB();

// Obter ação
$action = $_POST['action'] ?? '';

// Log para debug
error_log("=== WEAPONS HANDLER ===");
error_log("Action: " . $action);
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));

try {
    switch ($action) {
        case 'create':
            // Criar nova arma
            $nome = trim($_POST['nome']) ?? '';
            $tipo = $_POST['tipo'] ?? 'sem_categoria';
            $descricao = trim($_POST['descricao']) ?: null;
            $atk_bonus = (int)($_POST['atk_bonus'] ?? 0);
            $def_bonus = (int)($_POST['def_bonus'] ?? 0);
            $preco = (float)($_POST['preco'] ?? 0);
            $raridade = $_POST['raridade'] ?? 'comum';
            $nivel_minimo = (int)($_POST['nivel_minimo'] ?? 1);
            $durabilidade_max = (int)($_POST['durabilidade_max'] ?? 100);
            $peso = (int)($_POST['peso'] ?? 1);
            $elemento_afinidade = $_POST['elemento_afinidade'] ?: null;
            $efeito_especial = trim($_POST['efeito_especial']) ?: null;
            $classes_permitidas = $_POST['classes_permitidas'] ?? '[]';
            $ativo = (int)($_POST['ativo'] ?? 0);
            
            if (!$nome) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Nome é obrigatório']);
                exit;
            }
            
            // Processar upload de imagem
            $imagem = null;
            if (isset($_FILES['imagem_file']) && $_FILES['imagem_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 5) . '/public/img/weapons/';
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
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
                $uniqueName = uniqid('weapon_', true) . '.' . $extension;
                $uploadPath = $uploadDir . $uniqueName;
                
                // Mover arquivo
                if (move_uploaded_file($_FILES['imagem_file']['tmp_name'], $uploadPath)) {
                    $imagem = 'public/img/weapons/' . $uniqueName;
                } else {
                    ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload da imagem']);
                    exit;
                }
            }
            
            $stmt = $conn->prepare("
                INSERT INTO weapons (
                    nome, descricao, tipo, atk_bonus, def_bonus, preco, raridade,
                    nivel_minimo, durabilidade_max, peso, classes_permitidas,
                    elemento_afinidade, efeito_especial, imagem, ativo
                ) VALUES (
                    :nome, :descricao, :tipo, :atk_bonus, :def_bonus, :preco, :raridade,
                    :nivel_minimo, :durabilidade_max, :peso, :classes_permitidas,
                    :elemento_afinidade, :efeito_especial, :imagem, :ativo
                )
            ");
            
            $stmt->execute([
                'nome' => $nome,
                'descricao' => $descricao,
                'tipo' => $tipo,
                'atk_bonus' => $atk_bonus,
                'def_bonus' => $def_bonus,
                'preco' => $preco,
                'raridade' => $raridade,
                'nivel_minimo' => $nivel_minimo,
                'durabilidade_max' => $durabilidade_max,
                'peso' => $peso,
                'classes_permitidas' => $classes_permitidas,
                'elemento_afinidade' => $elemento_afinidade,
                'efeito_especial' => $efeito_especial,
                'imagem' => $imagem,
                'ativo' => $ativo
            ]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Arma criada com sucesso!'
            ]);
            exit;
            
        case 'update':
            // Atualizar arma existente
            $id = (int)$_POST['id'];
            $nome = trim($_POST['nome']) ?? '';
            $tipo = $_POST['tipo'] ?? 'sem_categoria';
            $descricao = trim($_POST['descricao']) ?: null;
            $atk_bonus = (int)($_POST['atk_bonus'] ?? 0);
            $def_bonus = (int)($_POST['def_bonus'] ?? 0);
            $preco = (float)($_POST['preco'] ?? 0);
            $raridade = $_POST['raridade'] ?? 'comum';
            $nivel_minimo = (int)($_POST['nivel_minimo'] ?? 1);
            $durabilidade_max = (int)($_POST['durabilidade_max'] ?? 100);
            $peso = (int)($_POST['peso'] ?? 1);
            $elemento_afinidade = $_POST['elemento_afinidade'] ?: null;
            $efeito_especial = trim($_POST['efeito_especial']) ?: null;
            $classes_permitidas = $_POST['classes_permitidas'] ?? '[]';
            $ativo = (int)($_POST['ativo'] ?? 0);
            $imagem = trim($_POST['imagem']) ?: null;
            
            if (!$id || !$nome) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
                exit;
            }
            
            // Buscar imagem atual
            $stmt = $conn->prepare("SELECT imagem FROM weapons WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $currentImage = $stmt->fetchColumn();
            
            // Processar upload de nova imagem
            if (isset($_FILES['imagem_file']) && $_FILES['imagem_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 5) . '/public/img/weapons/';
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
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
                $uniqueName = uniqid('weapon_', true) . '.' . $extension;
                $uploadPath = $uploadDir . $uniqueName;
                
                // Mover arquivo
                if (move_uploaded_file($_FILES['imagem_file']['tmp_name'], $uploadPath)) {
                    // Deletar imagem antiga se existir
                    if ($currentImage && file_exists(dirname(__DIR__, 5) . '/' . $currentImage)) {
                        unlink(dirname(__DIR__, 5) . '/' . $currentImage);
                    }
                    $imagem = 'public/img/weapons/' . $uniqueName;
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
                UPDATE weapons SET
                    nome = :nome,
                    descricao = :descricao,
                    tipo = :tipo,
                    atk_bonus = :atk_bonus,
                    def_bonus = :def_bonus,
                    preco = :preco,
                    raridade = :raridade,
                    nivel_minimo = :nivel_minimo,
                    durabilidade_max = :durabilidade_max,
                    peso = :peso,
                    classes_permitidas = :classes_permitidas,
                    elemento_afinidade = :elemento_afinidade,
                    efeito_especial = :efeito_especial,
                    imagem = :imagem,
                    ativo = :ativo
                WHERE id = :id
            ");
            
            $stmt->execute([
                'id' => $id,
                'nome' => $nome,
                'descricao' => $descricao,
                'tipo' => $tipo,
                'atk_bonus' => $atk_bonus,
                'def_bonus' => $def_bonus,
                'preco' => $preco,
                'raridade' => $raridade,
                'nivel_minimo' => $nivel_minimo,
                'durabilidade_max' => $durabilidade_max,
                'peso' => $peso,
                'classes_permitidas' => $classes_permitidas,
                'elemento_afinidade' => $elemento_afinidade,
                'efeito_especial' => $efeito_especial,
                'imagem' => $imagem,
                'ativo' => $ativo
            ]);
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Arma atualizada com sucesso!'
            ]);
            exit;
            
        case 'delete':
            // Deletar arma
            $id = (int)$_POST['id'];
            
            // Buscar imagem antes de deletar
            $stmt = $conn->prepare("SELECT imagem FROM weapons WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $imagem = $stmt->fetchColumn();
            
            // Deletar arma
            $stmt = $conn->prepare("DELETE FROM weapons WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            // Deletar imagem se existir
            if ($imagem && file_exists(dirname(__DIR__, 5) . '/' . $imagem)) {
                unlink(dirname(__DIR__, 5) . '/' . $imagem);
            }
            
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Arma excluída com sucesso!'
            ]);
            exit;
            
        default:
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
            exit;
    }
    
} catch (Exception $e) {
    error_log("Erro no weapons_handler: " . $e->getMessage());
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar operação: ' . $e->getMessage()
    ]);
    exit;
}
