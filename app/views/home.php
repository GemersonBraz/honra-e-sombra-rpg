<?php
$pageTitle = 'Início';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Hero Section - Campanha Narrativa -->
        <section class="relative overflow-hidden card p-8 lg:p-12 mb-12 animate-fade-in-up">
            <!-- Background decorativo -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-accent/10"></div>
            <div class="absolute top-0 right-0 w-1/3 h-full opacity-10">
                <div class="w-full h-full bg-gradient-to-l from-black/20 to-transparent"></div>
            </div>
            
            <div class="relative z-10 grid lg:grid-cols-2 gap-8 items-center">
                <!-- Conteúdo Principal -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        <p class="font-heading text-sm uppercase tracking-wider text-text/70">
                            Campanha Narrativa
                        </p>
                        <h1 class="font-title text-4xl lg:text-5xl font-bold text-primary leading-tight">
                            Escolha seu caminho entre<br>
                            <span class="text-primary honra-highlight">Honra</span> e 
                            <span class="text-accent">Sombra</span>.
                        </h1>
                    </div>
                    
                    <p class="font-heading text-lg text-text/80 leading-relaxed max-w-lg">
                        Um império à beira da guerra civil, clãs rivais e segredos antigos. 
                        Crie seu personagem e decida se sua lâmina servirá à lealdade ou à traição.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <?php if (!isLoggedIn()): ?>
                            <a href="index.php?page=register" 
                               class="btn-primary inline-flex items-center gap-2">
                                <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Criar" class="w-5 h-5 icon-white">
                                Criar Personagem
                            </a>
                            <a href="index.php?page=login" 
                               class="btn-outline inline-flex items-center gap-2">
                                <img src="<?= $basePath ?>img/icons-1x1/lorc/crossed-swords.svg" alt="Explorar" class="w-5 h-5 icon-white">
                                Explorar Regras
                            </a>
                        <?php else: ?>
                            <a href="index.php?page=dashboard" 
                               class="btn-primary inline-flex items-center gap-2">
                                <img src="<?= $basePath ?>img/icons-1x1/lorc/open-book.svg" alt="Dashboard" class="w-5 h-5 icon-white">
                                Meu Dashboard
                            </a>
                            <a href="index.php?page=personagens" 
                               class="btn-secondary inline-flex items-center gap-2">
                                <img src="<?= $basePath ?>img/icons-1x1/lorc/flat-star.svg" alt="Criar" class="w-5 h-5 icon-white">
                                Criar Personagem
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Imagem/Ilustração -->
                <div class="relative">
                    <div class="w-64 h-64 mx-auto lg:w-80 lg:h-80 relative">
                        <!-- Círculo de fundo marrom -->
                        <div class="absolute inset-0 rounded-full hero-logo-ring animate-pulse"></div>
                        
                        <!-- Logo central -->
                        <div class="absolute inset-0 flex items-center justify-center">
                       <img src="<?= $basePath ?>img/logo-honra-sombra.png" alt="Honra e Sombra" 
                           class="w-48 h-48 lg:w-64 lg:h-64 rounded-full shadow-2xl hero-logo-border animate-float">
                        </div>
                        
                        <!-- Elementos decorativos - Bolinhas marrons -->
                    <div class="absolute top-4 right-4 hero-dot hero-dot-lg animate-bounce" style="animation-delay:0.5s;"></div>
                    <div class="absolute bottom-8 left-4 hero-dot hero-dot-md animate-bounce" style="animation-delay:1s;"></div>
                    <div class="absolute top-1/2 right-0 hero-dot hero-dot-sm animate-bounce" style="animation-delay:1.5s;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Caminhos do Império -->
        <section class="mb-16">
            <div class="text-center mb-12">
                <h2 class="font-title text-3xl lg:text-4xl font-bold text-primary mb-4">
                    Caminhos do Império
                </h2>
                <p class="font-heading text-lg text-text/80 max-w-2xl mx-auto">
                    Diferentes filosofias e estilos de combate aguardam sua escolha
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Caminho da Honra -->
                <div class="card text-center border-l-4 border-primary hover:scale-105 transform duration-200">
                    <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/bordered-shield.svg" alt="Honra" class="w-10 h-10 icon-primary">
                    </div>
                    <div>
                        <h3 class="card-title">
                            Caminho da Honra
                        </h3>
                        <p class="card-description">
                            Samurais, generais e guerreiros leais a um código rígido. 
                            Foco em defesa, liderança e juramentos sagrados.
                        </p>
                    </div>
                </div>
                
                <!-- Caminho da Sombra -->
                <div class="card text-center border-l-4 border-secondary hover:scale-105 transform duration-200">
                    <div class="w-20 h-20 mx-auto bg-secondary/10 rounded-full flex items-center justify-center mb-6">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/ninja-mask.svg" alt="Sombra" class="w-10 h-10 icon-secondary">
                    </div>
                    <div>
                        <h3 class="card-title">
                            Caminho da Sombra
                        </h3>
                        <p class="card-description">
                            Ninjas, espiões e assassinos. Foco em furtividade, venenos 
                            e golpes precisos nas trevas.
                        </p>
                    </div>
                </div>
                
                <!-- Crônicas do Império -->
                <div class="card text-center border-l-4 border-accent hover:scale-105 transform duration-200">
                    <div class="w-20 h-20 mx-auto bg-accent/10 rounded-full flex items-center justify-center mb-6">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Crônicas" class="w-10 h-10 icon-accent">
                    </div>
                    <div>
                        <h3 class="card-title">
                            Crônicas do Império
                        </h3>
                        <p class="card-description">
                            Mapas, cidades e bestiário. Tudo que o mestre precisa para 
                            conduzir campanhas épicas.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Funcionalidades do Sistema -->
        <section class="card p-8 lg:p-12 mb-16">
            <div class="text-center mb-12">
                <h2 class="font-title text-3xl lg:text-4xl font-bold text-primary mb-4">
                    Sistema Completo de RPG
                </h2>
                <p class="font-heading text-lg text-text/80 max-w-3xl mx-auto">
                    Todas as ferramentas necessárias para mestres e jogadores em um só lugar
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                // Importar o componente
                require_once __DIR__ . '/../components/RPGComponents.php';
                
                // Configurações dos cards de funcionalidades
                $featureCards = [
                    [
                        'title' => '9 Classes Únicas',
                        'description' => 'Desde o furtivo Ninja até o poderoso Mago',
                        'icon' => 'crossed-swords',
                        'color' => 'primary',
                        'action' => 'Classes',
                        'buttonText' => 'Ver Classes →'
                    ],
                    [
                        'title' => '5 Elementos',
                        'description' => 'Fogo, Água, Terra, Ar e Vazio para magias',
                        'icon' => 'fire-ring',
                        'color' => 'secondary',
                        'action' => 'Elementos', 
                        'buttonText' => 'Ver Elementos →'
                    ],
                    [
                        'title' => 'Bestiário',
                        'description' => 'Criaturas místicas e monstros orientais',
                        'icon' => 'dragon-head',
                        'color' => 'accent',
                        'action' => 'Bestiário',
                        'buttonText' => 'Ver Bestiário →'
                    ],
                    [
                        'title' => 'Sistema de Magias',
                        'description' => 'Magias permanentes e temporárias únicas',
                        'icon' => 'wizard-staff',
                        'color' => 'purple',
                        'action' => 'Magias',
                        'buttonText' => 'Ver Magias →'
                    ]
                ];
                
                // Renderizar os cards usando o componente
                foreach ($featureCards as $card) {
                    echo RPGComponents::featureCard($card);
                }
                ?>
            </div>
        </section>

        <!-- Call to Action -->
        <?php if (!isLoggedIn()): ?>
        <section class="text-center bg-gradient-to-br from-primary to-accent text-white card p-12">
            <h2 class="font-title text-3xl lg:text-4xl font-bold mb-4">
                Pronto para Sua Aventura?
            </h2>
            <p class="font-heading text-lg mb-8 max-w-2xl mx-auto opacity-90">
                Junte-se a milhares de jogadores que já descobriram o universo de Honra e Sombra. 
                Crie seu personagem, escolha seu destino e escreva sua própria lenda.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="index.php?page=register" 
                   class="inline-flex items-center justify-center gap-2 bg-white text-gray-900 px-8 py-3 rounded-lg font-heading font-semibold hover:bg-gray-100 transition-colors duration-200 transform hover:scale-105 shadow-lg">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/flat-star.svg" alt="Estrela" class="w-5 h-5" style="filter: brightness(0) saturate(100%) invert(20%) sepia(8%) saturate(1456%) hue-rotate(169deg) brightness(100%) contrast(91%);">
                    Começar Agora - É Grátis!
                </a>
                <a href="index.php?page=login" 
                   class="inline-flex items-center justify-center gap-2 border-2 border-white text-white hover:bg-white hover:text-gray-900 px-8 py-3 rounded-lg font-heading font-semibold transition-colors duration-200">
                    <img src="<?= $basePath ?>img/icons-1x1/lorc/crossed-swords.svg" alt="Espadas" class="w-5 h-5 icon-white">
                    Já Tenho Conta
                </a>
            </div>
        </section>
        <?php else: ?>
        <section class="text-center bg-gradient-to-br from-primary to-secondary text-white card p-12">
            <h2 class="font-title text-3xl lg:text-4xl font-bold mb-4">
                Bem-vindo de Volta, <?= htmlspecialchars($_SESSION['user_nome'] ?? 'Guerreiro') ?>!
            </h2>
            <p class="font-heading text-lg mb-8 max-w-2xl mx-auto opacity-90">
                Sua jornada continua. Acesse seu dashboard para gerenciar seus personagens 
                ou explore as novas funcionalidades do sistema.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="index.php?page=dashboard" 
                   class="inline-flex items-center justify-center gap-2 bg-white text-gray-900 px-8 py-3 rounded-lg font-heading font-semibold hover:bg-gray-100 transition-colors duration-200 transform hover:scale-105 shadow-lg">
                    <img src="img/icons-1x1/lorc/open-book.svg" alt="Dashboard" class="w-5 h-5" style="filter: brightness(0) saturate(100%) invert(20%) sepia(8%) saturate(1456%) hue-rotate(169deg) brightness(100%) contrast(91%);">
                    Acessar Dashboard
                </a>
                <a href="index.php?page=personagens" 
                   class="inline-flex items-center justify-center gap-2 border-2 border-white text-white hover:bg-white hover:text-gray-900 px-8 py-3 rounded-lg font-heading font-semibold transition-colors duration-200">
                    <img src="img/icons-1x1/lorc/flat-star.svg" alt="Novo" class="w-5 h-5 icon-white">
                    Novo Personagem
                </a>
            </div>
        </section>
        <?php endif; ?>
        
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>