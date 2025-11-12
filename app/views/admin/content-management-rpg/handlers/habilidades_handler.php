<?php
/**
 * Handler para CRUD de Habilidades Disponíveis
 * Gerencia operações de criar, atualizar, deletar e listar habilidades
 */

// Configuração de erro e buffer
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Carregar configurações (já inicia a sessão)
    $configPath = dirname(__DIR__, 5) . '/config/db.php';
    if (!file_exists($configPath)) {
        throw new Exception('Arquivo de configuração não encontrado: ' . $configPath);
    }
    require_once $configPath;
    
    // Obter conexão com banco de dados
    $pdo = getDB();
    
    // Verificar se o usuário está logado e é admin
    if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
        throw new Exception('Acesso negado. Apenas administradores podem gerenciar habilidades.');
    }

    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    if (empty($action)) {
        throw new Exception('Ação não especificada.');
    }

    // ============================================
    // LISTAR HABILIDADES (para dropdown de pré-requisitos)
    // ============================================
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT id, nome FROM habilidades_disponiveis WHERE ativo = 1 ORDER BY nome");
        $habilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'habilidades' => $habilidades
        ]);
        exit;
    }

    // ============================================
    // CRIAR NOVA HABILIDADE
    // ============================================
    if ($action === 'create') {
        // Validar campos obrigatórios
        if (empty($_POST['nome'])) {
            throw new Exception('Nome da habilidade é obrigatório.');
        }
        if (empty($_POST['categoria'])) {
            throw new Exception('Categoria é obrigatória.');
        }
        if (empty($_POST['nivel_minimo'])) {
            throw new Exception('Nível mínimo é obrigatório.');
        }

        // Processar upload de imagem
        $imagemPath = null;
        if (isset($_FILES['imagemFile']) && $_FILES['imagemFile']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 5) . '/public/img/habilidades/';
            
            // Criar diretório se não existir
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Validar tipo de arquivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
            $fileType = $_FILES['imagemFile']['type'];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, GIF ou SVG.');
            }
            
            // Validar tamanho (2MB)
            if ($_FILES['imagemFile']['size'] > 2 * 1024 * 1024) {
                throw new Exception('A imagem deve ter no máximo 2MB.');
            }
            
            // Gerar nome único
            $extension = pathinfo($_FILES['imagemFile']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('habilidade_') . '.' . $extension;
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['imagemFile']['tmp_name'], $uploadFile)) {
                $imagemPath = 'public/img/habilidades/' . $fileName;
            }
        }

        // Preparar dados
        $nome = trim($_POST['nome']);
        $categoria = $_POST['categoria'];
        $tipo = trim($_POST['tipo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $nivel_minimo = $_POST['nivel_minimo'];
        $classes_permitidas = trim($_POST['classes_permitidas'] ?? '');
        $elemento_requerido = !empty($_POST['elemento_requerido']) ? $_POST['elemento_requerido'] : null;
        $bonus_atk = intval($_POST['bonus_atk'] ?? 0);
        $bonus_def = intval($_POST['bonus_def'] ?? 0);
        $bonus_hp = intval($_POST['bonus_hp'] ?? 0);
        $efeito_especial = trim($_POST['efeito_especial'] ?? '');
        $custo_pontos = intval($_POST['custo_pontos'] ?? 1);
        $prerequisito_habilidade_id = !empty($_POST['prerequisito_habilidade_id']) ? intval($_POST['prerequisito_habilidade_id']) : null;
        $ativo = isset($_POST['ativo']) && $_POST['ativo'] == '1' ? 1 : 0;
        $criado_por_admin = $_SESSION['user_id'];

        // Inserir no banco
        $sql = "INSERT INTO habilidades_disponiveis (
            nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, 
            elemento_requerido, bonus_atk, bonus_def, bonus_hp, efeito_especial, 
            custo_pontos, prerequisito_habilidade_id, imagem, ativo, criado_por_admin
        ) VALUES (
            :nome, :categoria, :tipo, :descricao, :nivel_minimo, :classes_permitidas,
            :elemento_requerido, :bonus_atk, :bonus_def, :bonus_hp, :efeito_especial,
            :custo_pontos, :prerequisito_habilidade_id, :imagem, :ativo, :criado_por_admin
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':categoria' => $categoria,
            ':tipo' => $tipo,
            ':descricao' => $descricao,
            ':nivel_minimo' => $nivel_minimo,
            ':classes_permitidas' => $classes_permitidas,
            ':elemento_requerido' => $elemento_requerido,
            ':bonus_atk' => $bonus_atk,
            ':bonus_def' => $bonus_def,
            ':bonus_hp' => $bonus_hp,
            ':efeito_especial' => $efeito_especial,
            ':custo_pontos' => $custo_pontos,
            ':prerequisito_habilidade_id' => $prerequisito_habilidade_id,
            ':imagem' => $imagemPath,
            ':ativo' => $ativo,
            ':criado_por_admin' => $criado_por_admin
        ]);

        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Habilidade criada com sucesso!'
        ]);
        exit;
    }

    // ============================================
    // ATUALIZAR HABILIDADE
    // ============================================
    if ($action === 'update') {
        if (empty($_POST['id'])) {
            throw new Exception('ID da habilidade não especificado.');
        }

        $id = intval($_POST['id']);

        // Buscar habilidade atual
        $stmt = $pdo->prepare("SELECT * FROM habilidades_disponiveis WHERE id = ?");
        $stmt->execute([$id]);
        $habilidadeAtual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$habilidadeAtual) {
            throw new Exception('Habilidade não encontrada.');
        }

        // Processar upload de nova imagem
        $imagemPath = $habilidadeAtual['imagem'];
        
        // Verificar se deve remover a imagem
        if (isset($_POST['removeImagem']) && $_POST['removeImagem'] == '1') {
            // Deletar arquivo antigo
            if ($imagemPath && file_exists(dirname(__DIR__, 5) . '/' . $imagemPath)) {
                unlink(dirname(__DIR__, 5) . '/' . $imagemPath);
            }
            $imagemPath = null;
        }
        
        // Upload de nova imagem
        if (isset($_FILES['imagemFile']) && $_FILES['imagemFile']['error'] === UPLOAD_ERR_OK) {
            // Deletar imagem antiga se existir
            if ($imagemPath && file_exists(dirname(__DIR__, 5) . '/' . $imagemPath)) {
                unlink(dirname(__DIR__, 5) . '/' . $imagemPath);
            }
            
            $uploadDir = dirname(__DIR__, 5) . '/public/img/habilidades/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
            $fileType = $_FILES['imagemFile']['type'];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Tipo de arquivo não permitido.');
            }
            
            if ($_FILES['imagemFile']['size'] > 2 * 1024 * 1024) {
                throw new Exception('A imagem deve ter no máximo 2MB.');
            }
            
            $extension = pathinfo($_FILES['imagemFile']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('habilidade_') . '.' . $extension;
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['imagemFile']['tmp_name'], $uploadFile)) {
                $imagemPath = 'public/img/habilidades/' . $fileName;
            }
        }

        // Preparar dados
        $nome = trim($_POST['nome']);
        $categoria = $_POST['categoria'];
        $tipo = trim($_POST['tipo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $nivel_minimo = $_POST['nivel_minimo'];
        $classes_permitidas = trim($_POST['classes_permitidas'] ?? '');
        $elemento_requerido = !empty($_POST['elemento_requerido']) ? $_POST['elemento_requerido'] : null;
        $bonus_atk = intval($_POST['bonus_atk'] ?? 0);
        $bonus_def = intval($_POST['bonus_def'] ?? 0);
        $bonus_hp = intval($_POST['bonus_hp'] ?? 0);
        $efeito_especial = trim($_POST['efeito_especial'] ?? '');
        $custo_pontos = intval($_POST['custo_pontos'] ?? 1);
        $prerequisito_habilidade_id = !empty($_POST['prerequisito_habilidade_id']) ? intval($_POST['prerequisito_habilidade_id']) : null;
        $ativo = isset($_POST['ativo']) && $_POST['ativo'] == '1' ? 1 : 0;

        // Atualizar no banco
        $sql = "UPDATE habilidades_disponiveis SET
            nome = :nome,
            categoria = :categoria,
            tipo = :tipo,
            descricao = :descricao,
            nivel_minimo = :nivel_minimo,
            classes_permitidas = :classes_permitidas,
            elemento_requerido = :elemento_requerido,
            bonus_atk = :bonus_atk,
            bonus_def = :bonus_def,
            bonus_hp = :bonus_hp,
            efeito_especial = :efeito_especial,
            custo_pontos = :custo_pontos,
            prerequisito_habilidade_id = :prerequisito_habilidade_id,
            imagem = :imagem,
            ativo = :ativo
        WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':categoria' => $categoria,
            ':tipo' => $tipo,
            ':descricao' => $descricao,
            ':nivel_minimo' => $nivel_minimo,
            ':classes_permitidas' => $classes_permitidas,
            ':elemento_requerido' => $elemento_requerido,
            ':bonus_atk' => $bonus_atk,
            ':bonus_def' => $bonus_def,
            ':bonus_hp' => $bonus_hp,
            ':efeito_especial' => $efeito_especial,
            ':custo_pontos' => $custo_pontos,
            ':prerequisito_habilidade_id' => $prerequisito_habilidade_id,
            ':imagem' => $imagemPath,
            ':ativo' => $ativo,
            ':id' => $id
        ]);

        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Habilidade atualizada com sucesso!'
        ]);
        exit;
    }

    // ============================================
    // DELETAR HABILIDADE
    // ============================================
    if ($action === 'delete') {
        if (empty($_POST['id'])) {
            throw new Exception('ID da habilidade não especificado.');
        }

        $id = intval($_POST['id']);

        // Buscar habilidade para deletar imagem
        $stmt = $pdo->prepare("SELECT imagem FROM habilidades_disponiveis WHERE id = ?");
        $stmt->execute([$id]);
        $habilidade = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($habilidade) {
            // Deletar imagem se existir
            if ($habilidade['imagem'] && file_exists(dirname(__DIR__, 5) . '/' . $habilidade['imagem'])) {
                unlink(dirname(__DIR__, 5) . '/' . $habilidade['imagem']);
            }

            // Deletar do banco
            $stmt = $pdo->prepare("DELETE FROM habilidades_disponiveis WHERE id = ?");
            $stmt->execute([$id]);

            ob_end_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Habilidade deletada com sucesso!'
            ]);
        } else {
            throw new Exception('Habilidade não encontrada.');
        }
        exit;
    }

    throw new Exception('Ação inválida.');

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
