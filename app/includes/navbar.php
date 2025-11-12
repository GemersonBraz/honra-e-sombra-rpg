<?php
require_once __DIR__ . '/functions.php';
// Verificar se as funções de sessão existem
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isLoggedIn() && isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin';
    }
}

$basePath = base_path();
?>

<!-- Header Principal -->
<header class="bg-primary text-nav shadow-xl relative overflow-hidden">
    <!-- Efeito de fundo decorativo -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white to-transparent transform -skew-x-12"></div>
    </div>
    
    <div class="container mx-auto px-4 py-4 relative z-10">
        <div class="flex items-center justify-between">
            <!-- Logo e Nome -->
            <div class="flex items-center space-x-4">
             <img src="<?= $basePath ?>img/logo-honra-sombra.png" alt="Logo Honra e Sombra" 
                 class="h-12 w-12 rounded-full border-2 border-white/30 navbar-logo">
                
                <div class="hidden sm:block">
                    <h1 class="font-title text-2xl font-bold tracking-wide text-nav">
                        <span class="text-yellow-200">HONRA</span> 
                        <span class="font-decorative text-sm mx-2">ou</span> 
                        <span class="text-blue-200">SOMBRA</span>
                    </h1>
                    <p class="font-heading text-sm text-white/80">Sistema de RPG</p>
                </div>
            </div>

            <!-- Navegação Principal -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="index.php" 
                   class="font-heading text-nav hover:text-yellow-200 transition-colors duration-200 flex items-center space-x-1 font-medium">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/castle.svg" alt="Início" class="w-4 h-4 icon-white">
                    <span>Início</span>
                </a>
                
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=login" 
                       class="font-heading text-nav hover:text-yellow-200 transition-colors duration-200 flex items-center space-x-1">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/crossed-swords.svg" alt="Entrar" class="w-4 h-4 icon-white">
                        <span>Entrar</span>
                    </a>
                    <a href="index.php?page=register" 
                       class="bg-secondary hover:opacity-90 px-4 py-2 rounded-lg font-heading font-semibold transition-all duration-200 text-white flex items-center gap-2">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Registrar" class="w-4 h-4 icon-white">
                        Juntar-se à Ordem
                    </a>
                <?php else: ?>
                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin" 
                           class="font-heading text-nav hover:text-yellow-200 transition-colors duration-200 flex items-center space-x-1">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Admin" class="w-4 h-4 icon-white">
                            <span>Painel Admin</span>
                        </a>
                        <a href="index.php?page=toast-demo" 
                           class="font-heading text-nav hover:text-yellow-200 transition-colors duration-200 flex items-center space-x-1">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Toast Demo" class="w-4 h-4 icon-white">
                            <span>Demo Toasts</span>
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=dashboard" 
                           class="font-heading text-nav hover:text-yellow-200 transition-colors duration-200 flex items-center space-x-1">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/open-book.svg" alt="Dashboard" class="w-4 h-4 icon-white">
                            <span>Dashboard</span>
                        </a>
                    <?php endif; ?>
                    
                    <div class="flex items-center space-x-3">
                        <?php if (isAdmin()): ?>
                            <a href="index.php?page=admin" 
                               class="bg-accent hover:opacity-90 px-3 py-1 rounded-lg text-sm font-heading font-semibold transition-all duration-200 text-white flex items-center gap-1">
                                <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Admin" class="w-3 h-3 icon-white">
                                Admin
                            </a>
                        <?php endif; ?>
                        
                        <?php
                            // Determina caminho do avatar do usuário (da sessão) ou fallback
                            $avatarSession = $_SESSION['user_avatar'] ?? null;
                            $avatarPath = $avatarSession && file_exists(__DIR__ . '/../../public/' . $avatarSession)
                                ? $avatarSession
                                : 'img/icons-1x1/lorc/trophy.svg';
                        ?>
                        <?php $displayName = $_SESSION['user_display_title'] ?? $_SESSION['user_nome'] ?? 'Guerreiro'; ?>
                        <span class="flex items-center gap-2 text-white/80 font-heading text-sm">
                            <img src="<?= $basePath . $avatarPath ?>" alt="Avatar" class="navbar-avatar w-8 h-8 rounded-full border-2 border-white/30 object-cover">
                            Olá, <span class="font-semibold text-yellow-200"><?= htmlspecialchars($displayName) ?></span>
                        </span>
                        
                        <a href="logout.php" 
                           class="bg-accent hover:opacity-90 px-3 py-2 rounded-lg font-heading font-semibold transition-all duration-200 text-sm text-white flex items-center gap-1">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/wooden-door.svg" alt="Sair" class="w-4 h-4 icon-white">
                            Sair
                        </a>
                    </div>
                <?php endif; ?>
            </nav>

            <!-- Menu Mobile -->
            <button class="md:hidden text-nav hover:text-yellow-200 transition-colors" 
                    id="mobileMenuToggle">
                <svg id="menuIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg id="closeIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Menu Mobile Expandido -->
        <div id="mobileMenu" class="mobile-menu md:hidden absolute top-full left-0 right-0 bg-primary border-t border-white/20 z-50">
            <nav class="container mx-auto px-4 py-4 flex flex-col space-y-3">
                <a href="index.php" class="font-heading text-nav hover:text-yellow-200 transition-colors flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/castle.svg" alt="Início" class="w-4 h-4 icon-white">
                    Início
                </a>
                
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=login" class="font-heading text-nav hover:text-blue-200 transition-colors flex items-center gap-2">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/crossed-swords.svg" alt="Entrar" class="w-4 h-4 icon-white">
                        Entrar
                    </a>
                    <a href="index.php?page=register" class="font-heading text-nav hover:text-yellow-200 transition-colors flex items-center gap-2">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Registrar" class="w-4 h-4 icon-white">
                        Registrar
                    </a>
                <?php else: ?>
                    <?php if (isAdmin()): ?>
                        <a href="index.php?page=admin" class="font-heading text-nav hover:text-yellow-200 transition-colors flex items-center gap-2">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Admin" class="w-4 h-4 icon-white">
                            Painel Admin
                        </a>
                        <a href="index.php?page=toast-demo" class="font-heading text-nav hover:text-yellow-200 transition-colors flex items-center gap-2">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Toast Demo" class="w-4 h-4 icon-white">
                            Demo Toasts
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=dashboard" class="font-heading text-nav hover:text-yellow-200 transition-colors flex items-center gap-2">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/open-book.svg" alt="Dashboard" class="w-4 h-4 icon-white">
                            Dashboard
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="font-heading text-nav hover:text-red-200 transition-colors flex items-center gap-2">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/wooden-door.svg" alt="Sair" class="w-4 h-4 icon-white">
                        Sair
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<!-- Navigation Breadcrumb -->
<?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
<nav class="bg-surface border-b border-border">
    <div class="container mx-auto px-4 py-2">
        <div class="flex items-center space-x-2 text-sm text-text/70">
            <a href="index.php" class="text-primary hover:text-accent transition-colors flex items-center gap-1">
                <img src="<?= $basePath ?>img/icons-1x1/lorc/castle.svg" alt="Início" class="w-4 h-4 icon-primary">
                Início
            </a>
            <?php foreach ($breadcrumb as $item): ?>
                <span>›</span>
                <?php if (isset($item['url'])): ?>
                    <a href="<?= $item['url'] ?>" class="text-primary hover:text-accent transition-colors">
                        <?= $item['title'] ?>
                    </a>
                <?php else: ?>
                    <span class="text-primary"><?= $item['title'] ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</nav>
<?php endif; ?>

<!-- Botão de Toggle de Tema Circular (Flutuante) -->
<button id="themeToggle" class="theme-toggle" title="Alternar tema">
    <img src="<?= $basePath ?>img/icons-1x1/lorc/moon.svg" alt="Tema" class="w-5 h-5 icon-white" id="themeIcon">
</button>

<!-- Messages (convertidas automaticamente para toasts) -->
<?php if (isset($_SESSION['message'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php 
        $messageType = $_SESSION['message_type'] ?? 'info';
        $message = addslashes($_SESSION['message']);
        $options = $_SESSION['message_options'] ?? [];
        $jsOptions = json_encode($options);
        ?>
        
        setTimeout(() => {
            if (window.toast && window.toast.<?= $messageType ?>) {
                window.toast.<?= $messageType ?>('<?= $message ?>', <?= $jsOptions ?>);
            }
        }, 200);
    });
</script>
<?php 
unset($_SESSION['message']);
unset($_SESSION['message_type']);
unset($_SESSION['message_options']);
endif; 
?>