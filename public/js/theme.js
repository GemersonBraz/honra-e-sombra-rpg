/**
 * Sistema de Troca de Temas Simples
 * Script minimalista para alternar entre Honra (claro) e Sombra (escuro)
 */

class ThemeSystem {
    constructor() {
        this.themes = {
            honra: {
                name: 'Honra',
                svgIcon: 'img/icons-1x1/lorc/sun.svg'
            },
            sombra: {
                name: 'Sombra',
                svgIcon: 'img/icons-1x1/lorc/moon.svg'
            }
        };

        this.currentTheme = this.getSavedTheme() || 'honra';
        this.init();
    }

    init() {
        // Não precisa aplicar novamente, já foi aplicado no inline script do header
        // Apenas atualizar o botão
        this.setupToggleButton();
    }

    /**
     * Aplica o tema selecionado
     */
    applyTheme(themeName) {
        document.documentElement.setAttribute('data-theme', themeName);
        this.currentTheme = themeName;
        this.saveTheme(themeName);

        // Atualizar botão se existir
        this.updateToggleButton();

        // Evento para outros scripts
        window.dispatchEvent(new CustomEvent('themeChanged', {
            detail: { theme: themeName }
        }));

        console.log(`🎨 Tema aplicado: ${themeName}`);
    }

    /**
     * Alterna entre os temas
     */
    toggleTheme() {
        const newTheme = this.currentTheme === 'honra' ? 'sombra' : 'honra';
        this.applyTheme(newTheme);
    }

    /**
     * Configura o botão de alternância (do navbar)
     */
    setupToggleButton() {
        // Aguardar o DOM carregar para encontrar o botão
        setTimeout(() => {
            const button = document.getElementById('themeToggle');
            if (button) {
                this.updateToggleButton(button);

                button.addEventListener('click', () => {
                    this.toggleTheme();
                });
            }
        }, 100);
    }

    /**
     * Atualiza ícone do botão
     */
    updateToggleButton(button = null) {
        const btn = button || document.getElementById('themeToggle');
        const iconElement = document.getElementById('themeIcon');

        if (!btn && !iconElement) return;

        const currentInfo = this.themes[this.currentTheme];

        if (iconElement) {
            // Se for um SVG, mudar o src
            if (iconElement.tagName === 'IMG') {
                iconElement.src = currentInfo.svgIcon;
                iconElement.alt = currentInfo.name;
            } else {
                // Fallback para emoji
                iconElement.textContent = currentInfo.icon;
            }
        }

        if (btn) {
            btn.title = `Tema atual: ${currentInfo.name}. Clique para alternar`;
        }
    }

    /**
     * Salva tema no localStorage
     */
    saveTheme(theme) {
        try {
            localStorage.setItem('honra-sombra-theme', theme);
        } catch (e) {
            console.warn('Não foi possível salvar tema:', e);
        }
    }

    /**
     * Recupera tema salvo
     */
    getSavedTheme() {
        try {
            return localStorage.getItem('honra-sombra-theme');
        } catch (e) {
            console.warn('Não foi possível recuperar tema:', e);
            return null;
        }
    }

    /**
     * Define um tema específico (para uso externo)
     */
    setTheme(themeName) {
        if (this.themes[themeName]) {
            this.applyTheme(themeName);
        }
    }

    /**
     * Retorna o tema atual
     */
    getCurrentTheme() {
        return this.currentTheme;
    }
}

// Inicialização automática
document.addEventListener('DOMContentLoaded', () => {
    window.themeSystem = new ThemeSystem();

    // Função global para uso fácil
    window.setTheme = (theme) => window.themeSystem.setTheme(theme);
    window.toggleTheme = () => window.themeSystem.toggleTheme();

    console.log('🎨 Sistema de Temas inicializado!');
});

// Detectar preferência do sistema
if (window.matchMedia && !localStorage.getItem('honra-sombra-theme')) {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');

    document.addEventListener('DOMContentLoaded', () => {
        if (prefersDark.matches) {
            window.themeSystem.setTheme('sombra');
        }
    });
}