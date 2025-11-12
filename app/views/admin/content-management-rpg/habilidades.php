<?php
/**
 * Gerenciamento de Habilidades
 */

// Verificar se é admin
if (!isAdmin()) {
    redirect('/');
    exit;
}

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/navbar.php';
?>

<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        <div class="card border-l-4 border-purple-500 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-purple-500/10 flex items-center justify-center">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crystal-wand.svg" alt="Habilidades" 
                             class="w-8 h-8 icon-primary">
                    </div>
                    <div>
                        <h1 class="font-title text-2xl font-bold text-primary">Gerenciar Habilidades</h1>
                        <p class="text-text/70 text-sm mt-1">Em desenvolvimento...</p>
                    </div>
                </div>
                <button onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/index'" 
                        class="btn-outline inline-flex items-center gap-2">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/arrow-dunk.svg" alt="Voltar" 
                         class="w-4 h-4 icon-muted rotate-180">
                    Voltar
                </button>
            </div>
        </div>
        
        <div class="card p-8 text-center">
            <p class="text-text/60">Esta página será implementada em breve!</p>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
