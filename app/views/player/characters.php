<?php
$pageTitle = 'Meus Personagens';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

// Só players podem acessar esta página
if (isAdmin()) {
    redirect('/admin');
}

$user = new User();
$userInfo = $user->getUserInfo($_SESSION['user_id']);
// TODO: Implementar sistema de personagens na Parte 2
?>

<!-- Main Content -->
<main class="flex-1 theme-bg-background theme-transition">
    <div class="container mx-auto px-4 py-8">
        
        <div class="theme-bg-surface rounded-xl p-8 mb-8 theme-transition border-l-4 border-blue-500">
            <h1 class="font-title text-3xl font-bold theme-text-primary flex items-center gap-3">
                <img src="img/icons-1x1/lorc/crossed-swords.svg" alt="Personagens" class="w-8 h-8 icon-primary">
                Meus Personagens
            </h1>
            <p class="font-heading theme-text-secondary mt-2">
                Gerencie seus heróis e suas aventuras no mundo de Honra e Sombra
            </p>
        </div>
        
        <div class="text-center py-12">
            <div class="w-24 h-24 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                <img src="img/icons-1x1/lorc/ninja-mask.svg" alt="Vazio" class="w-12 h-12 icon-muted">
            </div>
            <h2 class="text-2xl font-bold theme-text-primary mb-4">Nenhum Personagem Criado</h2>
            <p class="theme-text-secondary mb-6">
                Você ainda não possui personagens. Crie seu primeiro herói e comece sua jornada!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="systemToasts.featureComingSoon('Criação de Personagem')" 
                        class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-lg font-heading font-medium inline-flex items-center justify-center gap-2">
                    <img src="img/icons-1x1/lorc/flat-star.svg" alt="Criar" class="w-5 h-5 icon-white">
                    Criar Primeiro Personagem
                </button>
                <a href="index.php?page=dashboard" 
                   class="border border-gray-300 theme-text-primary hover:bg-gray-50 px-6 py-3 rounded-lg font-heading font-medium inline-flex items-center justify-center gap-2">
                    <img src="img/icons-1x1/lorc/castle.svg" alt="Voltar" class="w-5 h-5 icon-muted">
                    Voltar ao Dashboard
                </a>
            </div>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Cards de personagens serão implementados na Parte 2 -->
        </div>
        
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>