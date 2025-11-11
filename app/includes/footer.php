<?php
require_once __DIR__ . '/functions.php';
$basePath = base_path();
?>
    <!-- Footer -->
    <footer class="bg-surface border-t border-border mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-12">
                <div class="grid md:grid-cols-4 gap-8">
                    <!-- Logo e Descrição -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <img src="<?= $basePath ?>img/logo-honra-sombra.png" alt="Honra e Sombra" class="w-8 h-8 rounded-full">
                            <span class="font-title text-xl font-bold text-primary">Honra e Sombra</span>
                        </div>
                        <p class="font-heading text-text/80 text-sm">
                            Sistema completo de RPG oriental com classes únicas, elementos místicos e campanhas épicas.
                        </p>
                    </div>
                    
                    <!-- Links Rápidos -->
                    <div>
                        <h3 class="font-heading font-semibold text-primary mb-4">RPG</h3>
                        <ul class="space-y-2 text-sm">
                            <li><button onclick="systemToasts.featureComingSoon('Classes')" class="text-text/70 hover:text-primary transition-colors">Classes</button></li>
                            <li><button onclick="systemToasts.featureComingSoon('Regras')" class="text-text/70 hover:text-primary transition-colors">Regras</button></li>
                            <li><button onclick="systemToasts.featureComingSoon('Magias')" class="text-text/70 hover:text-primary transition-colors">Magias</button></li>
                            <li><button onclick="systemToasts.featureComingSoon('Bestiário')" class="text-text/70 hover:text-primary transition-colors">Bestiário</button></li>
                        </ul>
                    </div>
                    
                    <!-- Ferramentas -->
                    <div>
                        <h3 class="font-heading font-semibold text-primary mb-4">Ferramentas</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="index.php?page=dashboard" class="text-text/70 hover:text-primary transition-colors">Dashboard</a></li>
                            <li><button onclick="systemToasts.featureComingSoon('Criador de Personagem')" class="text-text/70 hover:text-primary transition-colors">Criador de Personagem</button></li>
                            <li><button onclick="systemToasts.featureComingSoon('Calculadora de Dano')" class="text-text/70 hover:text-primary transition-colors">Calculadora</button></li>
                            <li><button onclick="systemToasts.featureComingSoon('Gerador de NPCs')" class="text-text/70 hover:text-primary transition-colors">Gerador NPCs</button></li>
                        </ul>
                    </div>
                    
                    <!-- Conta -->
                    <div>
                        <h3 class="font-heading font-semibold text-primary mb-4">Conta</h3>
                        <ul class="space-y-2 text-sm">
                            <?php if (isLoggedIn()): ?>
                                <li><span class="text-text/70">Olá, <?= htmlspecialchars($_SESSION['user_nome']) ?>!</span></li>
                                <li><a href="index.php?page=dashboard" class="text-text/70 hover:text-primary transition-colors">Meu Dashboard</a></li>
                                <li><a href="logout.php" class="text-text/70 hover:text-primary transition-colors">Logout</a></li>
                            <?php else: ?>
                                <li><a href="index.php?page=login" class="text-text/70 hover:text-primary transition-colors">Entrar</a></li>
                                <li><a href="index.php?page=register" class="text-text/70 hover:text-primary transition-colors">Criar Conta</a></li>
                            <?php endif; ?>
                            <li><button onclick="systemToasts.featureComingSoon('Suporte')" class="text-text/70 hover:text-primary transition-colors">Suporte</button></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Linha inferior -->
                <div class="border-t border-border pt-8 mt-8 text-center">
                    <p class="text-text/70 text-sm mb-2">
                        &copy; 2024 Honra e Sombra RPG. Sistema criado com paixão
                    </p>
                    <p class="text-text/60 text-xs">
                        Ícones por <a href="https://game-icons.net" target="_blank" class="text-primary hover:text-accent transition-colors">game-icons.net</a> - 
                        Criados por <a href="https://game-icons.net/about.html#authors" target="_blank" class="text-primary hover:text-accent transition-colors">Lorc, Delapouite e vários artistas</a> 
                        sob licença <a href="https://creativecommons.org/licenses/by/3.0/" target="_blank" class="text-primary hover:text-accent transition-colors">CC BY 3.0</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= $basePath ?>js/theme.js?v=<?= time() ?>"></script>
    <script src="<?= $basePath ?>js/toast-fixed.js?v=<?= time() ?>"></script>
    <script src="<?= $basePath ?>js/toast-helpers.js?v=<?= time() ?>"></script>
    
    <!-- Scroll animations and mobile menu -->
    <script>
        // Inicializar sistema de temas
        const themeSystem = new ThemeSystem();
        
        // Função para animar elementos na tela
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            elements.forEach(element => {
                observer.observe(element);
            });
        }
        
        // Função para mobile menu
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');
            
            if (menu.classList.contains('open')) {
                menu.classList.remove('open');
                menuIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            } else {
                menu.classList.add('open');
                menuIcon.classList.add('hidden');
                closeIcon.classList.remove('hidden');
            }
        }
        
        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar animações
            animateOnScroll();
            
            // Event listener para menu mobile
            const mobileToggle = document.getElementById('mobileMenuToggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', toggleMobileMenu);
            }
            
            // Fechar menu mobile quando clicar em um link
            const mobileLinks = document.querySelectorAll('#mobileMenu a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    const menu = document.getElementById('mobileMenu');
                    if (menu && menu.classList.contains('open')) {
                        toggleMobileMenu();
                    }
                });
            });
        });
    </script>
</body>
</html>