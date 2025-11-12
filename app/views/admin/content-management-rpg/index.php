<?php
/**
 * Gerenciamento de Conteúdo RPG
 * Classes, Habilidades, Golpes Templates e Elementos
 */

// Verificar se é admin
if (!isAdmin()) {
    redirect('/');
    exit;
}

// Incluir header
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/navbar.php';

// Conectar ao banco
$conn = getDB();

// Contar registros de cada tabela
try {
    $countClasses = $conn->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    $countHabilidades = $conn->query("SELECT COUNT(*) FROM habilidades_disponiveis")->fetchColumn();
    $countGolpes = $conn->query("SELECT COUNT(*) FROM golpes_templates")->fetchColumn();
    $countElementos = $conn->query("SELECT COUNT(*) FROM elementos")->fetchColumn();
} catch (Exception $e) {
    error_log("Erro ao contar registros: " . $e->getMessage());
    $countClasses = 0;
    $countHabilidades = 0;
    $countGolpes = 0;
    $countElementos = 0;
}
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Card de Título Principal -->
        <div class="card border-l-4 border-primary p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/gems.svg" alt="Conteúdo RPG" 
                             class="w-9 h-9 icon-primary">
                    </div>
                    <div>
                        <h1 class="font-title text-3xl font-bold text-primary flex items-center gap-3">
                            Gerenciamento de Conteúdo RPG
                        </h1>
                        <p class="font-heading text-text/80 mt-2">
                            Gerencie classes, habilidades, golpes especiais e elementos do sistema.
                        </p>
                    </div>
                </div>
                
                <button onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin'" 
                        class="btn-outline inline-flex items-center gap-2">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/castle.svg" alt="Dashboard" 
                         class="w-4 h-4 icon-muted">
                    Voltar ao Dashboard
                </button>
            </div>
        </div>
        
        <!-- Grid de Cards-Botão -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Card Classes -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-blue-500 cursor-pointer"
                 onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/classes'">
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/helmet-head-shot.svg" alt="Classes" 
                             class="w-10 h-10 icon-primary">
                    </div>
                    <h3 class="font-heading text-xl font-semibold text-primary mb-2">Classes</h3>
                    <p class="text-text/70 text-sm mb-3">
                        Gerencie as classes de personagens disponíveis no sistema.
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400"><?= $countClasses ?></span>
                        <span class="text-sm text-text/70">cadastradas</span>
                    </div>
                </div>
            </div>
            
            <!-- Card Habilidades -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-purple-500 cursor-pointer"
                 onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/habilidades'">
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crystal-wand.svg" alt="Habilidades" 
                             class="w-10 h-10 icon-primary">
                    </div>
                    <h3 class="font-heading text-xl font-semibold text-primary mb-2">Habilidades</h3>
                    <p class="text-text/70 text-sm mb-3">
                        Gerencie habilidades, magias e técnicas disponíveis.
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <span class="text-2xl font-bold text-purple-600 dark:text-purple-400"><?= $countHabilidades ?></span>
                        <span class="text-sm text-text/70">cadastradas</span>
                    </div>
                </div>
            </div>
            
            <!-- Card Golpes Especiais -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-red-500 cursor-pointer"
                 onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/golpes'">
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/sword-clash.svg" alt="Golpes" 
                             class="w-10 h-10 icon-primary">
                    </div>
                    <h3 class="font-heading text-xl font-semibold text-primary mb-2">Golpes Especiais</h3>
                    <p class="text-text/70 text-sm mb-3">
                        Gerencie golpes e técnicas especiais de combate.
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 dark:bg-rose-900/20 rounded-lg border border-rose-200 dark:border-rose-800">
                        <span class="text-2xl font-bold text-rose-600 dark:text-rose-400"><?= $countGolpes ?></span>
                        <span class="text-sm text-text/70">cadastrados</span>
                    </div>
                </div>
            </div>
            
            <!-- Card Elementos -->
            <div class="card hover:scale-105 transform duration-200 border-l-4 border-orange-500 cursor-pointer"
                 onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/elementos'">
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-4">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/burning-embers.svg" alt="Elementos" 
                             class="w-10 h-10 icon-primary">
                    </div>
                    <h3 class="font-heading text-xl font-semibold text-primary mb-2">Elementos</h3>
                    <p class="text-text/70 text-sm mb-3">
                        Gerencie elementos mágicos e suas interações.
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                        <span class="text-2xl font-bold text-amber-600 dark:text-amber-400"><?= $countElementos ?></span>
                        <span class="text-sm text-text/70">cadastrados</span>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</main>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
