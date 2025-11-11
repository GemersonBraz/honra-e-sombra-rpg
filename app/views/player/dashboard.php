<?php
$pageTitle = 'Dashboard';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$user = new User();
$userInfo = $user->getUserInfo($_SESSION['user_id']);
?>

<!-- Main Content -->
<main class="flex-1 theme-bg-background theme-transition">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header do Dashboard -->
        <div class="card p-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-title text-3xl font-bold text-primary flex items-center gap-3">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/castle.svg" alt="Dashboard" class="w-8 h-8 icon-primary">
                        Dashboard do Guerreiro
                    </h1>
                    <p class="font-heading text-text/70 mt-2">
                        Bem-vindo, <span class="font-semibold text-primary"><?= htmlspecialchars($userInfo['nome']) ?></span>! 
                        <span class="text-accent font-semibold">- Jogador</span>
                    </p>
                </div>
                
                <div class="text-right">
                    <div class="text-sm text-text/60 font-heading">
                        <p>Último acesso: <?= date('d/m/Y H:i', strtotime($userInfo['ultimo_acesso'] ?? 'now')) ?></p>
                        <p>Membro desde: <?= date('M/Y', strtotime($userInfo['data_criacao'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cards de Status -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/60 font-heading text-sm">Personagens</p>
                        <p class="text-2xl font-bold text-primary">0</p>
                    </div>
                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/crossed-swords.svg" alt="Personagens" class="w-5 h-5 icon-primary">
                    </div>
                </div>
                <p class="text-xs text-text/50 font-heading mt-2">Será implementado na Parte 2</p>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/60 font-heading text-sm">Campanhas</p>
                        <p class="text-2xl font-bold text-primary">0</p>
                    </div>
                    <div class="w-10 h-10 bg-secondary/10 rounded-full flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Campanhas" class="w-5 h-5 icon-secondary">
                    </div>
                </div>
                <p class="text-xs text-text/50 font-heading mt-2">Será implementado na Parte 5</p>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/60 font-heading text-sm">Nível</p>
                        <p class="text-2xl font-bold text-primary">1</p>
                    </div>
                    <div class="w-10 h-10 bg-accent/10 rounded-full flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/flat-star.svg" alt="Nível" class="w-5 h-5 icon-accent">
                    </div>
                </div>
                <p class="text-xs text-text/50 font-heading mt-2">Sistema de XP futuro</p>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-text/60 font-heading text-sm">Conquistas</p>
                        <p class="text-2xl font-bold text-primary">1</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-500/10 rounded-full flex items-center justify-center">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/trophy.svg" alt="Conquistas" class="w-5 h-5 icon-purple">
                    </div>
                </div>
                <p class="text-xs text-text/50 font-heading mt-2">Primeiro Login</p>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php
            // Importar o componente
            require_once __DIR__ . '/../../components/RPGComponents.php';
            
            // Configurações dos cards de ações
            $actionCards = [
                [
                    'title' => 'Criar Personagem',
                    'description' => 'Monte seu primeiro herói escolhendo classe, atributos e histórico.',
                    'icon' => 'bordered-shield',
                    'color' => 'primary',
                    'action' => 'Personagens',
                    'buttonText' => 'Ver Personagens'
                ],
                [
                    'title' => 'Estudar Regras',
                    'description' => 'Aprenda sobre classes, elementos, habilidades e sistema de combate.',
                    'icon' => 'open-book',
                    'color' => 'secondary',
                    'action' => 'Manual de Regras',
                    'buttonText' => 'Em Breve - Parte 3'
                ],
                [
                    'title' => 'Ferramentas',
                    'description' => 'Calculadoras de dano, geradores de NPCs e outras utilidades.',
                    'icon' => 'gears',
                    'color' => 'purple',
                    'action' => 'Ferramentas de Jogo',
                    'buttonText' => 'Em Breve - Parte 4'
                ]
            ];
            
            // Renderizar os cards usando o componente
            foreach ($actionCards as $index => $card) {
                if ($index === 0) {
                    // Card especial de personagens com redirecionamento direto
                    echo "
                    <!-- {$card['title']} -->
                    <div class=\"text-center space-y-4 p-6 rounded-lg bg-surface/50 border border-border hover:border-yellow-600 transition-all\">
                        <div class=\"w-16 h-16 mx-auto bg-yellow-500/10 rounded-full flex items-center justify-center hover:bg-yellow-500/20 hover:scale-110 transition-all\">
                            <img src=\"img/icons-1x1/lorc/{$card['icon']}.svg\" alt=\"{$card['title']}\" class=\"w-8 h-8 hover:scale-110 transition-transform\" style=\"filter: brightness(0) saturate(100%) invert(55%) sepia(73%) saturate(558%) hue-rotate(353deg) brightness(97%) contrast(88%);\">
                        </div>
                        <h3 class=\"font-heading text-lg font-semibold text-primary\">{$card['title']}</h3>
                        <p class=\"text-text/70 text-sm\">
                            {$card['description']}
                        </p>
                        <button onclick=\"window.location.href='index.php?page=characters'\" 
                                class=\"text-yellow-600 hover:text-yellow-500 font-heading font-medium text-sm transition-colors\">
                            {$card['buttonText']} →
                        </button>
                    </div>";
                } else {
                    echo RPGComponents::featureCard($card);
                }
            }
            ?>
        </div>
        
        <!-- Informações da Conta -->
        <div class="card p-6">
            <h3 class="font-heading text-lg font-semibold text-primary mb-4 flex items-center gap-2">
                <img src="img/icons-1x1/lorc/gear-hammer.svg" alt="Configurações" class="w-5 h-5 icon-primary">
                Informações da Conta
            </h3>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-heading font-medium text-primary mb-3">Dados Pessoais</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-text/60">Nome:</span>
                            <span class="text-primary font-medium"><?= htmlspecialchars($userInfo['nome']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text/60">Email:</span>
                            <span class="text-primary font-medium"><?= htmlspecialchars($userInfo['email']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text/60">Tipo de Conta:</span>
                            <span class="text-primary font-medium"><?= ucfirst($userInfo['tipo']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-heading font-medium text-primary mb-3">Estatísticas</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-text/60">Conta criada em:</span>
                            <span class="text-primary font-medium"><?= date('d/m/Y', strtotime($userInfo['data_criacao'])) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text/60">Último acesso:</span>
                            <span class="text-primary font-medium"><?= date('d/m/Y H:i', strtotime($userInfo['ultimo_acesso'] ?? 'now')) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text/60">Status:</span>
                            <span class="text-emerald-600 font-medium flex items-center gap-1">
                                <img src="img/icons-1x1/lorc/checked-shield.svg" alt="Ativa" class="w-4 h-4" style="filter: brightness(0) saturate(100%) invert(45%) sepia(86%) saturate(492%) hue-rotate(95deg) brightness(101%) contrast(101%);">
                                Ativa
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-border">
                <div class="flex flex-wrap gap-4">
                    <button onclick="systemToasts.featureComingSoon('Editar Perfil')" 
                            class="btn-secondary inline-flex items-center gap-2">
                        <img src="img/icons-1x1/lorc/quill-ink.svg" alt="Editar" class="w-4 h-4 icon-white">
                        Editar Perfil
                    </button>
                    <button onclick="systemToasts.featureComingSoon('Alterar Senha')" 
                            class="btn-outline inline-flex items-center gap-2">
                        <img src="img/icons-1x1/lorc/key.svg" alt="Senha" class="w-4 h-4 icon-muted">
                        Alterar Senha
                    </button>
                    <button onclick="systemToasts.featureComingSoon('Configurações')" 
                            class="btn-outline inline-flex items-center gap-2">
                        <img src="img/icons-1x1/lorc/gear-hammer.svg" alt="Config" class="w-4 h-4 icon-muted">
                        Configurações
                    </button>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>