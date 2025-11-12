<?php
/**
 * Handler para operações CRUD de Golpes Especiais
 */

// Configurar headers para JSON
header('Content-Type: application/json');

// Iniciar controle de erros
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Log de debug
error_log("golpes_handler.php - Iniciando processamento");
error_log("POST: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));

try {
    // Incluir configuração do banco de dados
    require_once dirname(__DIR__, 5) . '/config/db.php';
    
    // Verificar se usuário está logado e é admin
    if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'admin') {
        error_log("Erro: Usuário não autorizado. Session: " . print_r($_SESSION, true));
        throw new Exception('Acesso negado. Apenas administradores podem gerenciar golpes.');
    }
    
    // Obter conexão com banco de dados
    $pdo = getDB();
    
    // Obter ação
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    error_log("Ação recebida: " . $action);
    
    switch ($action) {
        
        case 'list':
            // Listar golpes ativos para selects
            $stmt = $pdo->query("SELECT id, nome FROM golpes_templates WHERE ativo = 1 ORDER BY nome");
            $golpes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'golpes' => $golpes
            ]);
            break;
            
        case 'create':
            // Validar campos obrigatórios
            if (empty($_POST['nome']) || empty($_POST['tipo'])) {
                throw new Exception('Nome e tipo são obrigatórios.');
            }
            
            // Preparar dados
            $nome = trim($_POST['nome']);
            $tipo = $_POST['tipo'];
            $categoria = trim($_POST['categoria'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $dano_base = (int)($_POST['dano_base'] ?? 0);
            $dano_extra = (int)($_POST['dano_extra'] ?? 0);
            $bonus_defesa = (int)($_POST['bonus_defesa'] ?? 0);
            $duracao_rodadas = (int)($_POST['duracao_rodadas'] ?? 1);
            $elemento = !empty($_POST['elemento']) ? $_POST['elemento'] : null;
            $nivel_minimo = $_POST['nivel_minimo'] ?? 'principiante';
            $classes_permitidas = trim($_POST['classes_permitidas'] ?? '');
            $habilidade_requerida_id = !empty($_POST['habilidade_requerida_id']) ? (int)$_POST['habilidade_requerida_id'] : null;
            $usos_maximos = (int)($_POST['usos_maximos'] ?? 3);
            $custo_pontos = (int)($_POST['custo_pontos'] ?? 1);
            $efeitos_especiais = trim($_POST['efeitos_especiais'] ?? '');
            $ativo = isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1;
            $criado_por_admin = $_SESSION['user_id'] ?? null;
            
            // Upload de imagem
            $imagem = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 5) . '/public/img/golpes/';
                
                // Criar diretório se não existir
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Validar arquivo
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['imagem']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP.');
                }
                
                // Validar tamanho (2MB)
                if ($_FILES['imagem']['size'] > 2 * 1024 * 1024) {
                    throw new Exception('Arquivo muito grande. Tamanho máximo: 2MB.');
                }
                
                // Gerar nome único
                $extension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $filename = 'golpe_' . time() . '_' . uniqid() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                // Mover arquivo
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $filepath)) {
                    $imagem = $filename;
                } else {
                    throw new Exception('Erro ao fazer upload da imagem.');
                }
            }
            
            // Inserir no banco
            $sql = "INSERT INTO golpes_templates (
                        nome, tipo, categoria, descricao, dano_base, dano_extra, bonus_defesa,
                        duracao_rodadas, elemento, nivel_minimo, classes_permitidas, 
                        habilidade_requerida_id, usos_maximos, custo_pontos, efeitos_especiais,
                        imagem, ativo, criado_por_admin
                    ) VALUES (
                        :nome, :tipo, :categoria, :descricao, :dano_base, :dano_extra, :bonus_defesa,
                        :duracao_rodadas, :elemento, :nivel_minimo, :classes_permitidas,
                        :habilidade_requerida_id, :usos_maximos, :custo_pontos, :efeitos_especiais,
                        :imagem, :ativo, :criado_por_admin
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':tipo' => $tipo,
                ':categoria' => $categoria,
                ':descricao' => $descricao,
                ':dano_base' => $dano_base,
                ':dano_extra' => $dano_extra,
                ':bonus_defesa' => $bonus_defesa,
                ':duracao_rodadas' => $duracao_rodadas,
                ':elemento' => $elemento,
                ':nivel_minimo' => $nivel_minimo,
                ':classes_permitidas' => $classes_permitidas,
                ':habilidade_requerida_id' => $habilidade_requerida_id,
                ':usos_maximos' => $usos_maximos,
                ':custo_pontos' => $custo_pontos,
                ':efeitos_especiais' => $efeitos_especiais,
                ':imagem' => $imagem,
                ':ativo' => $ativo,
                ':criado_por_admin' => $criado_por_admin
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Golpe criado com sucesso!',
                'id' => $pdo->lastInsertId()
            ]);
            break;
            
        case 'update':
            // Validar campos obrigatórios
            if (empty($_POST['id']) || empty($_POST['nome']) || empty($_POST['tipo'])) {
                throw new Exception('ID, nome e tipo são obrigatórios.');
            }
            
            $id = (int)$_POST['id'];
            
            // Buscar golpe existente
            $stmt = $pdo->prepare("SELECT imagem FROM golpes_templates WHERE id = ?");
            $stmt->execute([$id]);
            $golpeAtual = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$golpeAtual) {
                throw new Exception('Golpe não encontrado.');
            }
            
            // Preparar dados
            $nome = trim($_POST['nome']);
            $tipo = $_POST['tipo'];
            $categoria = trim($_POST['categoria'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $dano_base = (int)($_POST['dano_base'] ?? 0);
            $dano_extra = (int)($_POST['dano_extra'] ?? 0);
            $bonus_defesa = (int)($_POST['bonus_defesa'] ?? 0);
            $duracao_rodadas = (int)($_POST['duracao_rodadas'] ?? 1);
            $elemento = !empty($_POST['elemento']) ? $_POST['elemento'] : null;
            $nivel_minimo = $_POST['nivel_minimo'] ?? 'principiante';
            $classes_permitidas = trim($_POST['classes_permitidas'] ?? '');
            $habilidade_requerida_id = !empty($_POST['habilidade_requerida_id']) ? (int)$_POST['habilidade_requerida_id'] : null;
            $usos_maximos = (int)($_POST['usos_maximos'] ?? 3);
            $custo_pontos = (int)($_POST['custo_pontos'] ?? 1);
            $efeitos_especiais = trim($_POST['efeitos_especiais'] ?? '');
            $ativo = isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1;
            
            // Manter imagem atual ou fazer upload de nova
            $imagem = $golpeAtual['imagem'];
            
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(__DIR__, 5) . '/public/img/golpes/';
                
                // Criar diretório se não existir
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Validar arquivo
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['imagem']['type'];
                
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP.');
                }
                
                // Validar tamanho (2MB)
                if ($_FILES['imagem']['size'] > 2 * 1024 * 1024) {
                    throw new Exception('Arquivo muito grande. Tamanho máximo: 2MB.');
                }
                
                // Deletar imagem antiga se existir
                if ($imagem && file_exists($uploadDir . $imagem)) {
                    unlink($uploadDir . $imagem);
                }
                
                // Gerar nome único
                $extension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $filename = 'golpe_' . time() . '_' . uniqid() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                
                // Mover arquivo
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $filepath)) {
                    $imagem = $filename;
                } else {
                    throw new Exception('Erro ao fazer upload da imagem.');
                }
            }
            
            // Atualizar no banco
            $sql = "UPDATE golpes_templates SET
                        nome = :nome,
                        tipo = :tipo,
                        categoria = :categoria,
                        descricao = :descricao,
                        dano_base = :dano_base,
                        dano_extra = :dano_extra,
                        bonus_defesa = :bonus_defesa,
                        duracao_rodadas = :duracao_rodadas,
                        elemento = :elemento,
                        nivel_minimo = :nivel_minimo,
                        classes_permitidas = :classes_permitidas,
                        habilidade_requerida_id = :habilidade_requerida_id,
                        usos_maximos = :usos_maximos,
                        custo_pontos = :custo_pontos,
                        efeitos_especiais = :efeitos_especiais,
                        imagem = :imagem,
                        ativo = :ativo
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':tipo' => $tipo,
                ':categoria' => $categoria,
                ':descricao' => $descricao,
                ':dano_base' => $dano_base,
                ':dano_extra' => $dano_extra,
                ':bonus_defesa' => $bonus_defesa,
                ':duracao_rodadas' => $duracao_rodadas,
                ':elemento' => $elemento,
                ':nivel_minimo' => $nivel_minimo,
                ':classes_permitidas' => $classes_permitidas,
                ':habilidade_requerida_id' => $habilidade_requerida_id,
                ':usos_maximos' => $usos_maximos,
                ':custo_pontos' => $custo_pontos,
                ':efeitos_especiais' => $efeitos_especiais,
                ':imagem' => $imagem,
                ':ativo' => $ativo,
                ':id' => $id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Golpe atualizado com sucesso!'
            ]);
            break;
            
        case 'delete':
            // Validar ID
            if (empty($_POST['id'])) {
                throw new Exception('ID do golpe é obrigatório.');
            }
            
            $id = (int)$_POST['id'];
            
            // Buscar golpe para deletar imagem
            $stmt = $pdo->prepare("SELECT imagem FROM golpes_templates WHERE id = ?");
            $stmt->execute([$id]);
            $golpe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$golpe) {
                throw new Exception('Golpe não encontrado.');
            }
            
            // Deletar imagem se existir
            if ($golpe['imagem']) {
                $uploadDir = dirname(__DIR__, 5) . '/public/img/golpes/';
                $filepath = $uploadDir . $golpe['imagem'];
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }
            
            // Deletar do banco
            $stmt = $pdo->prepare("DELETE FROM golpes_templates WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Golpe deletado com sucesso!'
            ]);
            break;
            
        case 'get':
            // Obter dados de um golpe específico
            if (empty($_GET['id'])) {
                throw new Exception('ID do golpe é obrigatório.');
            }
            
            $id = (int)$_GET['id'];
            
            $stmt = $pdo->prepare("SELECT * FROM golpes_templates WHERE id = ?");
            $stmt->execute([$id]);
            $golpe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$golpe) {
                throw new Exception('Golpe não encontrado.');
            }
            
            echo json_encode([
                'success' => true,
                'golpe' => $golpe
            ]);
            break;
            
        default:
            throw new Exception('Ação inválida.');
    }
    
} catch (Exception $e) {
    error_log("Erro no handler: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
