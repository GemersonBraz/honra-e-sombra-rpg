<?php
$pageTitle = 'Painel Administrativo';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$user = new User();
$userInfo = $user->getUserInfo($_SESSION['user_id']);

// Estatísticas do sistema
$stats = [
    'total_users' => $user->getUserCount(), // Conta usuários ativos no banco
    'total_characters' => 0, // Será implementado na Parte 2
    'total_campaigns' => 0, // Será implementado na Parte 5
    'active_sessions' => 1 // Será implementado
];
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header do Dashboard Admin -->
        <div class="card border-l-4 border-primary p-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-title text-3xl font-bold text-primary flex items-center gap-3">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Admin" class="w-8 h-8 icon-primary">
                        Painel Administrativo
                    </h1>
                    <p class="font-heading text-text/80 mt-2">
                        Bem-vindo, <span class="font-semibold text-accent"><?= htmlspecialchars($userInfo['nome']) ?></span> - <span class="text-primary font-semibold">Administrador</span>
                    </p>
                </div>
                
                <div class="text-right">
                    <div class="text-sm text-text/70 font-heading">
                        <p>Último acesso: <?= date('d/m/Y H:i', strtotime($userInfo['ultimo_acesso'] ?? 'now')) ?></p>
                        <p>Admin desde: <?= date('M/Y', strtotime($userInfo['data_criacao'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ações Administrativas Rápidas -->
        <div class="card mb-8">
            <h3 class="font-heading text-lg font-semibold text-primary mb-6 border-b border-border pb-2 flex items-center gap-2">
                <img src="<?= $basePath ?>img/icons-1x1/lorc/archery-target.svg" alt="Ações" class="w-5 h-5 icon-primary">
                Ações Rápidas
            </h3>
            
            <div class="flex flex-wrap gap-4">
                <button onclick="window.location.href='index.php?page=admin/users'" 
                        class="btn-primary inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/crowned-skull.svg" alt="Usuários" class="w-4 h-4 icon-white">
                    Ver Usuários
                </button>
                <a href="index.php?page=perfil" 
                   class="btn-secondary inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/quill-ink.svg" alt="Perfil" class="w-4 h-4 icon-white">
                    Editar Perfil
                </a>
                <button onclick="systemToasts.backupComplete()" 
                        class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-heading text-sm inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Backup" class="w-4 h-4 icon-white">
                    Backup Agora
                </button>
                <button onclick="systemToasts.featureComingSoon('Logs Detalhados')" 
                        class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg font-heading text-sm inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/papers.svg" alt="Logs" class="w-4 h-4 icon-white">
                    Ver Logs
                </button>
                <button onclick="systemToasts.maintenance()" 
                        class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg font-heading text-sm inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/hammer-nails.svg" alt="Manutenção" class="w-4 h-4 icon-white">
                    Modo Manutenção
                </button>
                <a href="index.php?page=toast-demo" 
                   class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-lg font-heading text-sm inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/sliced-bread.svg" alt="Toast" class="w-4 h-4 icon-white">
                    Demo Toasts
                </a>
                <button onclick="systemToasts.featureComingSoon('Configurações Avançadas')" 
                        class="btn-outline inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/gears.svg" alt="Configurações" class="w-4 h-4 icon-muted">
                    Configurações
                </button>
                <button onclick="themeSystem.toggleTheme()" 
                        class="btn-secondary inline-flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/moon.svg" alt="Tema" class="w-4 h-4 icon-white">
                    Alternar Tema
                </button>
            </div>
        </div>
        
        <!-- Cards de Estatísticas do Sistema -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="card border-l-4 border-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/70 font-heading text-sm">Total de Usuários</p>
                        <p class="text-2xl font-bold text-primary"><?= $stats['total_users'] ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-surface flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/crowned-skull.svg" alt="Usuários" 
                             class="w-7 h-7 icon-primary">
                    </div>
                </div>
                <p class="text-xs text-text/60 font-heading mt-2">Incluindo admins e players</p>
            </div>
            
            <div class="card border-l-4 border-secondary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/70 font-heading text-sm">Personagens</p>
                        <p class="text-2xl font-bold text-primary"><?= $stats['total_characters'] ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-surface flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/battle-axe.svg" alt="Personagens" 
                             class="w-7 h-7 icon-secondary">
                    </div>
                </div>
                <p class="text-xs text-text/60 font-heading mt-2">Será implementado na Parte 2</p>
            </div>
            
            <div class="card border-l-4 border-accent">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/70 font-heading text-sm">Campanhas</p>
                        <p class="text-2xl font-bold text-primary"><?= $stats['total_campaigns'] ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-surface flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Campanhas" 
                             class="w-7 h-7 icon-accent">
                    </div>
                </div>
                <p class="text-xs text-text/60 font-heading mt-2">Será implementado na Parte 5</p>
            </div>
            
            <div class="card border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/70 font-heading text-sm">Sessões Ativas</p>
                        <p class="text-2xl font-bold text-primary"><?= $stats['active_sessions'] ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-surface flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/cubes.svg" alt="Sessões" 
                             class="w-7 h-7" 
                             style="filter: brightness(0) saturate(100%) invert(67%) sepia(59%) saturate(558%) hue-rotate(120deg) brightness(95%) contrast(90%);">
                    </div>
                </div>
                <p class="text-xs text-text/60 font-heading mt-2">Usuários online agora</p>
            </div>
        </div>
        
        <!-- Ferramentas Administrativas -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Gerenciamento de Usuários -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-blue-500">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/crowned-skull.svg" alt="Usuários" class="w-8 h-8 icon-primary">
                    </div>
                    <h3 class="font-heading text-lg font-semibold text-primary mb-2">Gerenciar Usuários</h3>
                    <p class="text-text/70 text-sm mb-4">
                        Visualizar, editar, ativar/desativar contas de usuários do sistema.
                    </p>
                    <button onclick="window.location.href='index.php?page=admin/users'" 
                            class="btn-primary">
                        Ver Usuários
                    </button>
                </div>
            </div>
            
            <!-- Sistema de Backup -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-green-500">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Backup" class="w-8 h-8 icon-primary">
                    </div>
                    <h3 class="font-heading text-lg font-semibold text-primary mb-2">Backup do Sistema</h3>
                    <p class="text-text/70 text-sm mb-4">
                        Fazer backup do banco de dados e arquivos do sistema.
                    </p>
                    <button onclick="systemToasts.backupComplete()" 
                            class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-heading font-medium text-sm">
                        Simular Backup
                    </button>
                </div>
            </div>
            
            <!-- Configurações do Sistema -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-purple-500">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/gears.svg" alt="Configurações" class="w-8 h-8 icon-primary">
                    </div>
                    <h3 class="font-heading text-lg font-semibold text-primary mb-2">Configurações</h3>
                    <p class="text-text/70 text-sm mb-4">
                        Configurar parâmetros globais, manutenção e atualizações.
                    </p>
                    <button onclick="systemToasts.featureComingSoon('Configurações Admin')" 
                            class="bg-purple-600 hover:bg-purple-500 text-white px-4 py-2 rounded-lg font-heading font-medium text-sm">
                        Em Breve - Parte 6
                    </button>
                </div>
            </div>
            
            <!-- Logs do Sistema -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-yellow-500">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/papers.svg" alt="Logs" class="w-8 h-8 icon-primary">
                    </div>
                    <h3 class="font-heading text-lg font-semibold text-primary mb-2">Logs e Relatórios</h3>
                    <p class="text-text/70 text-sm mb-4">
                        Visualizar logs de sistema, estatísticas e relatórios.
                    </p>
                    <button onclick="systemToasts.featureComingSoon('Logs do Sistema')" 
                            class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg font-heading font-medium text-sm">
                        Em Breve - Parte 6
                    </button>
                </div>
            </div>
            
            <!-- Conteúdo RPG -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-red-500">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/gems.svg" alt="Conteúdo" class="w-8 h-8 icon-primary">
                    </div>
                    <h3 class="font-heading text-lg font-semibold text-primary mb-2">Conteúdo RPG</h3>
                    <p class="text-text/70 text-sm mb-4">
                        Gerenciar classes, habilidades, magias e bestiário.
                    </p>
                    <button onclick="systemToasts.featureComingSoon('Gerenciamento de Conteúdo')" 
                            class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg font-heading font-medium text-sm">
                        Em Breve - Parte 3
                    </button>
                </div>
            </div>
            
            <!-- Manutenção -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-orange-500">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/hammer-nails.svg" alt="Manutenção" class="w-8 h-8 icon-primary">
                    </div>
                    <h3 class="font-heading text-lg font-semibold text-primary mb-2">Modo Manutenção</h3>
                    <p class="text-text/70 text-sm mb-4">
                        Ativar modo de manutenção para atualizações do sistema.
                    </p>
                    <button onclick="systemToasts.maintenance()" 
                            class="bg-orange-600 hover:bg-orange-500 text-white px-4 py-2 rounded-lg font-heading font-medium text-sm">
                        Simular Manutenção
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Informações do Sistema -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Informações da Conta Admin -->
            <div class="card">
                <h3 class="font-heading text-lg font-semibold text-primary mb-4 border-b border-border pb-2 flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Admin" class="w-5 h-5 icon-primary">
                    Conta Administrativa
                </h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-text/70">Nome:</span>
                        <span class="text-primary font-medium"><?= htmlspecialchars($userInfo['nome']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">Email:</span>
                        <span class="text-primary font-medium"><?= htmlspecialchars($userInfo['email']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">Nível:</span>
                        <span class="text-red-600 font-bold flex items-center gap-1">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Admin" class="w-4 h-4" style="filter: brightness(0) saturate(100%) invert(16%) sepia(100%) saturate(7485%) hue-rotate(3deg) brightness(91%) contrast(107%);">
                            ADMINISTRADOR
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">Admin desde:</span>
                        <span class="text-primary font-medium"><?= date('d/m/Y', strtotime($userInfo['data_criacao'])) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">Último acesso:</span>
                        <span class="text-primary font-medium"><?= date('d/m/Y H:i', strtotime($userInfo['ultimo_acesso'] ?? 'now')) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Status do Sistema -->
            <div class="card">
                <h3 class="font-heading text-lg font-semibold text-primary mb-4 border-b border-border pb-2 flex items-center gap-2">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/lightning-frequency.svg" alt="Status" class="w-5 h-5 icon-primary">
                    Status do Sistema
                </h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-text/70">Versão:</span>
                        <span class="text-primary font-medium">1.0 - Parte 1</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">PHP:</span>
                        <span class="text-primary font-medium"><?= phpversion() ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">Servidor:</span>
                        <span class="text-green-600 font-medium flex items-center gap-1">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/checked-shield.svg" alt="Online" class="w-4 h-4" style="filter: brightness(0) saturate(100%) invert(45%) sepia(86%) saturate(492%) hue-rotate(95deg) brightness(101%) contrast(101%);">
                            Online
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">Banco de Dados:</span>
                        <span class="text-green-600 font-medium flex items-center gap-1">
                            <img src="<?= $basePath ?>img/icons-1x1/lorc/checked-shield.svg" alt="Conectado" class="w-4 h-4" style="filter: brightness(0) saturate(100%) invert(45%) sepia(86%) saturate(492%) hue-rotate(95deg) brightness(101%) contrast(101%);">
                            Conectado
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text/70">Último Backup:</span>
                        <span class="text-primary font-medium">Nunca</span>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>