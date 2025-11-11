<?php
$pageTitle = 'Gerenciamento de Usuários - Admin';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

// Só admins podem acessar esta página
if (!isAdmin()) {
    redirect('/dashboard');
}

$user = new User();
// TODO: Implementar listagem de usuários nas próximas partes
?>

<!-- Main Content -->
<main class="flex-1 theme-bg-background theme-transition">
    <div class="container mx-auto px-4 py-8">
        
        <div class="theme-bg-surface rounded-xl p-8 mb-8 theme-transition border-l-4 border-red-500">
            <h1 class="font-title text-3xl font-bold theme-text-primary flex items-center gap-3">
                <img src="<?= $basePath ?>img/icons-1x1/lorc/crowned-skull.svg" alt="Usuários" class="w-8 h-8 icon-primary">
                Gerenciamento de Usuários
            </h1>
            <p class="font-heading theme-text-secondary mt-2">
                Painel administrativo para gerenciar todos os usuários do sistema
            </p>
        </div>
        
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                <img src="<?= $basePath ?>img/icons-1x1/lorc/crenulated-shield.svg" alt="Em desenvolvimento" class="w-12 h-12 icon-muted">
            </div>
            <h2 class="text-2xl font-bold theme-text-primary mb-4">Em Desenvolvimento</h2>
            <p class="theme-text-secondary mb-6">
                Esta funcionalidade será implementada na Parte 6 do sistema.
            </p>
            <a href="index.php?page=admin" 
               class="bg-red-600 hover:bg-red-500 text-white px-6 py-3 rounded-lg font-heading font-medium inline-flex items-center gap-2">
                <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Voltar" class="w-5 h-5 icon-white">
                Voltar ao Painel Admin
            </a>
        </div>
        
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>