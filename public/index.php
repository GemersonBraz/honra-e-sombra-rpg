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
        include __DIR__ . '/../app/views/admin/users.php';
        break;
        
    case '/toast-demo':
        // Página de demonstração dos toasts
        include __DIR__ . '/../app/views/toast-demo.php';
        break;
        
    case '/admin':
        requireAdmin();
        include __DIR__ . '/../app/views/admin/dashboard.php';
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