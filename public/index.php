<?php
/**
 * Arquivo Principal - Sistema Honra e Sombra RPG
 * Router simples para gerenciar as rotas da aplicação
 */

// Verificar se os arquivos existem antes de incluir
$configPath = __DIR__ . '/../config/db.php';
$userModelPath = __DIR__ . '/../app/models/User.php';

if (!file_exists($configPath)) {
    die('Erro: Arquivo config/db.php não encontrado em: ' . $configPath);
}

if (!file_exists($userModelPath)) {
    die('Erro: Arquivo User.php não encontrado em: ' . $userModelPath);
}

require_once $configPath;
require_once $userModelPath;

// Função para gerar URLs corretamente
function url($path = '') {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $baseUrl = dirname($scriptName);
    if ($baseUrl === '/') $baseUrl = '';
    return $baseUrl . $path;
}

// Disponibilizar a função globalmente
$GLOBALS['url'] = 'url';

// Obter a URL requisitada
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remover parte do caminho base se necessário (funciona com diferentes configurações)
$possibleBasePaths = ['/Honra-e-Sombra/public', '/Honra-e-Sombra', '/public'];

foreach ($possibleBasePaths as $basePath) {
    if (strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath));
        break;
    }
}

// Se estiver vazio, definir como raiz
if (empty($path) || $path === '/') {
    $path = '/';
}

// Se estivermos acessando via public/index.php diretamente, verificar parâmetro 'page'
if ((strpos($path, '/index.php') !== false || basename($_SERVER['SCRIPT_NAME']) === 'index.php') && isset($_GET['page'])) {
    $path = '/' . $_GET['page'];
} elseif (strpos($path, '/index.php') !== false || (empty($path) && basename($_SERVER['SCRIPT_NAME']) === 'index.php')) {
    $path = '/';
}

// Verificar proteção de rotas que exigem login
$protectedRoutes = ['/dashboard', '/personagens', '/admin'];
function requireLogin() {
    if (!isLoggedIn()) {
        setMessage('Você precisa fazer login para acessar esta página.', 'error');
        redirect('/login');
    }
}

// Verificar proteção de rotas de admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setMessage('Acesso negado. Apenas administradores podem acessar esta área.', 'error');
        redirect('/dashboard');
    }
}

// Router Principal
switch ($path) {
    case '/':
    case '':
        include __DIR__ . '/../app/views/home.php';
        break;
        
    case '/register':
        // Redirecionar se já estiver logado
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('/admin');
            } else {
                redirect('/dashboard');
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $errors = $user->validate($_POST);
            
            if (empty($errors)) {
                $result = $user->create(
                    sanitize($_POST['nome']),
                    sanitize($_POST['email']),
                    $_POST['senha']
                );
                
                if ($result['success']) {
                    setMessage('Conta criada com sucesso! Faça login para continuar.', 'success', [
                        'title' => 'Bem-vindo à Ordem',
                        'action' => [
                            'text' => 'Fazer Login',
                            'handler' => 'window.location.href="index.php?page=login"'
                        ]
                    ]);
                    redirect('/login');
                } else {
                    setMessage($result['message'], 'error');
                }
            }
        }
        include __DIR__ . '/../app/views/register.php';
        break;
        
    case '/login':
        // Redirecionar se já estiver logado
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $errors = $user->validate($_POST, true);
            
            if (empty($errors)) {
                $result = $user->login(
                    sanitize($_POST['email']),
                    $_POST['senha']
                );
                
                if ($result['success']) {
                    // Redirecionar baseado no tipo de usuário
                    if ($_SESSION['user_tipo'] === 'admin') {
                        setMessage('Login administrativo realizado com sucesso!', 'success', [
                            'title' => 'Acesso Administrativo',
                            'action' => [
                                'text' => 'Painel Admin',
                                'handler' => 'window.location.href="index.php?page=admin"'
                            ]
                        ]);
                        redirect('/admin');
                    } else {
                        setMessage('Login realizado com sucesso!', 'success', [
                            'title' => 'Acesso Liberado',
                            'action' => [
                                'text' => 'Ir para Dashboard',
                                'handler' => 'window.location.href="index.php?page=dashboard"'
                            ]
                        ]);
                        redirect('/dashboard');
                    }
                } else {
                    setMessage($result['message'], 'error');
                }
            }
        }
        include __DIR__ . '/../app/views/login.php';
        break;
        
    case '/logout':
        if (isLoggedIn()) {
            $user = new User();
            $user->logout();
            setMessage('Logout realizado com sucesso!');
        }
        redirect('/');
        break;
        
    case '/dashboard':
        requireLogin();
        // Se for admin, redirecionar para painel admin
        if (isAdmin()) {
            redirect('/admin');
        }
        include __DIR__ . '/../app/views/player/dashboard.php';
        break;
        
    case '/personagens':
        requireLogin();
        // Apenas players podem acessar personagens
        if (isAdmin()) {
            redirect('/admin');
        }
        include __DIR__ . '/../app/views/player/characters.php';
        break;
        
    case '/personagens/criar':
        requireLogin();
        // TODO: Implementar na PARTE 2
        setMessage('Criação de personagens será implementada na Parte 2!', 'error');
        redirect('/personagens');
        break;
        
    case '/admin/users':
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $action = $_POST['action'] ?? '';
            $userId = (int)($_POST['user_id'] ?? 0);
            
            if ($action === 'edit' && $userId > 0) {
                $data = [];
                if (isset($_POST['nome'])) $data['nome'] = trim($_POST['nome']);
                if (isset($_POST['email'])) $data['email'] = trim($_POST['email']);
                if (isset($_POST['tipo'])) $data['tipo'] = $_POST['tipo'];
                if (isset($_POST['ativo'])) $data['ativo'] = (int)$_POST['ativo'];
                
                $res = $user->updateUser($userId, $data);
                if ($res['success']) {
                    setMessage('Usuário atualizado com sucesso!', 'success');
                } else {
                    setMessage($res['message'] ?? 'Erro ao atualizar usuário.', 'error');
                }
            }
            
            if ($action === 'delete' && $userId > 0) {
                $res = $user->deleteUser($userId);
                if ($res['success']) {
                    setMessage('Usuário deletado com sucesso!', 'success');
                } else {
                    setMessage($res['message'] ?? 'Erro ao deletar usuário.', 'error');
                }
            }
            
            if ($action === 'reset_password' && $userId > 0) {
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (strlen($newPassword) < 6) {
                    setMessage('Senha deve ter pelo menos 6 caracteres.', 'error');
                } elseif ($newPassword !== $confirmPassword) {
                    setMessage('Senhas não conferem.', 'error');
                } else {
                    $res = $user->resetPassword($userId, $newPassword);
                    if ($res['success']) {
                        setMessage('Senha resetada com sucesso!', 'success');
                    } else {
                        setMessage($res['message'] ?? 'Erro ao resetar senha.', 'error');
                    }
                }
            }
            
            redirect('/admin/users');
        }
        include __DIR__ . '/../app/views/admin/users.php';
        break;
        
    case '/admin/content-management-rpg/index':
    case '/admin/content-management-rpg':
        requireAdmin();
        include __DIR__ . '/../app/views/admin/content-management-rpg/index.php';
        break;
    
    case '/admin/content-management-rpg/classes':
        requireAdmin();
        include __DIR__ . '/../app/views/admin/content-management-rpg/classes.php';
        break;
    
    case '/admin/content-management-rpg/habilidades':
        requireAdmin();
        include __DIR__ . '/../app/views/admin/content-management-rpg/habilidades.php';
        break;
    
    case '/admin/content-management-rpg/golpes':
        requireAdmin();
        include __DIR__ . '/../app/views/admin/content-management-rpg/golpes.php';
        break;
    
    case '/admin/content-management-rpg/elementos':
        requireAdmin();
        include __DIR__ . '/../app/views/admin/content-management-rpg/elementos.php';
        break;
    
    case '/toast-demo':
        // Página de demonstração dos toasts
        include __DIR__ . '/../app/views/toast-demo.php';
        break;
        
    case '/admin':
        requireAdmin();
        include __DIR__ . '/../app/views/admin/dashboard.php';
        break;

    case '/perfil':
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $userId = (int)($_SESSION['user_id'] ?? 0);
            $updated = false;
            $action = $_POST['action'] ?? 'avatar';

            // 1) Atualização de avatar (upload)
            if ($action === 'avatar' && isset($_FILES['avatar_upload']) && is_array($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar_upload'];
                $maxSize = 2 * 1024 * 1024; // 2MB
                $allowedExt = ['jpg','jpeg','png','webp'];
                $allowedMime = ['image/jpeg','image/png','image/webp'];
                if ($file['size'] > $maxSize) {
                    setMessage('Arquivo muito grande (máx. 2MB).', 'error');
                    redirect('/perfil');
                }
                // Verifica extensão e MIME
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
                    setMessage('Formato de imagem não permitido. Use JPG, PNG ou WEBP.', 'error');
                    redirect('/perfil');
                }
                // Gera nome e diretório
                $uploadDir = __DIR__ . '/img/avatars/uploads';
                if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);
                $filename = 'user_' . $userId . '_' . time() . '.' . $ext;
                $dest = $uploadDir . '/' . $filename;
                if (!move_uploaded_file($file['tmp_name'], $dest)) {
                    setMessage('Falha ao salvar o arquivo enviado.', 'error');
                    redirect('/perfil');
                }
                // Caminho relativo público
                $publicPath = 'img/avatars/uploads/' . $filename;
                $res = $user->updateAvatar($userId, $publicPath);
                if ($res['success']) {
                    // Limitar a 3 uploads por usuário (apagar os mais antigos)
                    $pattern = $uploadDir . '/user_' . $userId . '_*';
                    $userFiles = glob($pattern);
                    if ($userFiles && count($userFiles) > 3) {
                        // Ordena por data de criação (mais antiga primeiro)
                        usort($userFiles, function($a, $b) { return filectime($a) <=> filectime($b); });
                        while (count($userFiles) > 3) {
                            $old = array_shift($userFiles);
                            @unlink($old);
                        }
                    }
                    setMessage('Avatar atualizado com sucesso!', 'success');
                    $updated = true;
                } else {
                    setMessage($res['message'] ?? 'Não foi possível atualizar o avatar.', 'error');
                }
            }

            // 2) Seleção de avatar padrão/uploads existentes
            if (!$updated && $action === 'avatar' && isset($_POST['avatar_preset'])) {
                $preset = trim($_POST['avatar_preset']);
                // Permitir apenas caminhos dentro de pastas conhecidas
                $allowedPrefixes = [
                    'img/avatars/defaults/',
                    'img/icons-1x1/lorc/',
                    'img/avatars/uploads/'
                ];
                $allowed = false;
                foreach ($allowedPrefixes as $prefix) {
                    if (strpos($preset, $prefix) === 0) { $allowed = true; break; }
                }
                $filePath = __DIR__ . '/' . $preset;
                if ($allowed && file_exists($filePath)) {
                    $res = $user->updateAvatar($userId, $preset);
                    if ($res['success']) {
                        setMessage('Avatar atualizado com sucesso!', 'success');
                        $updated = true;
                    } else {
                        setMessage($res['message'] ?? 'Não foi possível atualizar o avatar.', 'error');
                    }
                } else {
                    setMessage('Avatar selecionado inválido.', 'error');
                }
            }

            // 3) Atualizar dados de perfil (display name e bio)
            if ($action === 'profile') {
                $display = isset($_POST['display_title']) ? trim($_POST['display_title']) : '';
                $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
                // Validações simples
                if ($display !== '' && (mb_strlen($display) < 2 || mb_strlen($display) > 40)) {
                    setMessage('Nome de exibição deve ter entre 2 e 40 caracteres.', 'error');
                    redirect('/perfil');
                }
                if (mb_strlen($bio) > 500) {
                    setMessage('Descrição deve ter no máximo 500 caracteres.', 'error');
                    redirect('/perfil');
                }
                $res = $user->updateProfile($userId, $display ?: null, $bio ?: null);
                if ($res['success']) {
                    setMessage('Perfil atualizado com sucesso!', 'success');
                } else {
                    setMessage($res['message'] ?? 'Não foi possível atualizar o perfil.', 'error');
                }
            }

            // 4) Trocar senha
            if ($action === 'change_password') {
                $current = $_POST['current_password'] ?? '';
                $new = $_POST['new_password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';
                if (strlen($new) < 6) {
                    setMessage('A nova senha deve ter pelo menos 6 caracteres.', 'error');
                    redirect('/perfil');
                }
                if ($new !== $confirm) {
                    setMessage('A confirmação de senha não confere.', 'error');
                    redirect('/perfil');
                }
                $res = $user->changePassword($userId, $current, $new);
                if ($res['success']) {
                    setMessage('Senha alterada com sucesso!', 'success');
                } else {
                    setMessage($res['message'] ?? 'Não foi possível alterar a senha.', 'error');
                }
            }

            // 5) Deletar avatar upload específico
            if ($action === 'delete_avatar' && isset($_POST['delete_file'])) {
                $fileRel = trim($_POST['delete_file']);
                // Apenas arquivos do próprio usuário dentro de uploads
                if (strpos($fileRel, 'img/avatars/uploads/user_' . $userId . '_') === 0) {
                    $fullPath = __DIR__ . '/' . $fileRel;
                    if (is_file($fullPath)) {
                        // Se é o avatar atual, limpar da sessão antes de deletar
                        if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar'] === $fileRel) {
                            unset($_SESSION['user_avatar']);
                        }
                        if (@unlink($fullPath)) {
                            setMessage('Avatar removido com sucesso.', 'success');
                        } else {
                            setMessage('Falha ao remover arquivo.', 'error');
                        }
                    } else {
                        setMessage('Arquivo não encontrado.', 'error');
                    }
                } else {
                    setMessage('Operação inválida.', 'error');
                }
            }

            redirect('/perfil');
        }
        include __DIR__ . '/../app/views/profile.php';
        break;
        
    // Páginas públicas (serão implementadas nas próximas partes)
    case '/classes':
        setMessage('Página de classes será implementada na Parte 3!', 'error');
        redirect('/');
        break;
        
    case '/elementos':
        setMessage('Página de elementos será implementada na Parte 3!', 'error');
        redirect('/');
        break;
        
    case '/habilidades':
        setMessage('Página de habilidades será implementada na Parte 3!', 'error');
        redirect('/');
        break;
        
    case '/skills':
        setMessage('Página de skills será implementada na Parte 4!', 'error');
        redirect('/');
        break;
        
    case '/magias':
        setMessage('Página de magias será implementada na Parte 4!', 'error');
        redirect('/');
        break;
        
    case '/bestiario':
        setMessage('Bestiário será implementado na Parte 5!', 'error');
        redirect('/');
        break;
        
    case '/regras':
    case '/cidades':
    case '/cursos':
        setMessage('Esta seção será implementada em partes futuras!', 'error');
        redirect('/');
        break;
        
    default:
        // Página 404
        http_response_code(404);
        $pageTitle = 'Página não encontrada';
        include __DIR__ . '/../app/includes/header.php';
        include __DIR__ . '/../app/includes/navbar.php';
        ?>
        <!-- Main Content -->
        <main class="flex-1 theme-bg-background theme-transition">
            <div class="container mx-auto px-4 py-8">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🗡️</div>
                    <h1 class="text-4xl font-bold theme-text-primary mb-4">404 - Página não encontrada</h1>
                    <p class="theme-text-secondary mb-6">A página que você procura não existe no reino de Honra e Sombra.</p>
                    <a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                        🏠 Voltar ao Início
                    </a>
                </div>
            </div>
        </main>
        <?php
        include __DIR__ . '/../app/includes/footer.php';
        break;
}
?>