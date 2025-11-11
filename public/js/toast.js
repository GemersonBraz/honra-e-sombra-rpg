/**
 * Sistema de Toast para Honra e Sombra RPG - Versão Otimizada
 * Utiliza Tailwind CSS para estilização
 */

class ToastSystem {
    constructor() {
        this.container = this.createContainer();
        this.maxToasts = 5;
        this.defaultDuration = 2000; // 2 segundos como padrão
    }

    createContainer() {
        // Remover container existente se houver
        const existing = document.getElementById('toast-container');
        if (existing) existing.remove();

        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-[9999] space-y-2 pointer-events-none';
        container.style.cssText = `
            width: 320px;
            max-width: calc(100vw - 2rem);
        `;
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'false');

        document.body.appendChild(container);
        return container;
    }

    show(message, type = 'info', duration = null, options = {}) {
        if (!message) return null;

        duration = duration || this.getDurationByType(type);
        const toast = this.createToast(message, type, { ...options, duration: duration });

        // Limitar número de toasts
        this.enforceLimits();

        this.container.appendChild(toast);

        // Animação de entrada
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        });

        // Auto-remover
        if (duration > 0) {
            setTimeout(() => this.remove(toast), duration);
        }

        return toast;
    }

    createToast(message, type, options = {}) {
        const toast = document.createElement('div');
        const config = this.getToastConfig(type);
        const duration = options.duration || this.getDurationByType(type);

        // Configuração básica do toast
        toast.style.cssText = `
            width: 100%;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease-out;
            pointer-events: auto;
            cursor: default;
        `;

        toast.className = `
            ${config.bg} ${config.border} ${config.text}
            border-l-4 rounded-lg shadow-lg p-4 
            hover:shadow-xl transition-shadow duration-200
        `.replace(/\s+/g, ' ').trim();
        toast.setAttribute('role', 'alert');

        // HTML estruturado
        const title = options.title ? `<div class="font-semibold text-sm ${config.titleColor} mb-1">${this.escapeHtml(options.title)}</div>` : '';
        const actionButton = options.action ? `
            <button class="toast-action-btn mt-2 text-sm font-medium ${config.actionColor} hover:underline focus:outline-none"
                    ${typeof options.action.handler === 'string' ? `onclick="${options.action.handler}"` : ''}
                    type="button" role="button" aria-label="${this.escapeHtml(options.action.text)}">
                ${this.escapeHtml(options.action.text)}
            </button>
        ` : '';

        const progress = options.progress !== false ? `
            <div class="mt-3 bg-gray-200 rounded-full h-1 overflow-hidden">
                <div class="${config.progressBar} h-full rounded-full" 
                     style="width: 100%; animation: progressShrink ${duration / 1000}s linear forwards;"></div>
            </div>
        ` : '';

        toast.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-3">
                    ${config.icon}
                </div>
                <div class="flex-1 min-w-0">
                    ${title}
                    <div class="text-sm ${config.messageColor} break-words leading-5">
                        ${this.escapeHtml(message)}
                    </div>
                    ${actionButton}
                </div>
                <div class="flex-shrink-0 ml-3">
                    <button class="toast-close p-1 rounded text-gray-400 hover:text-gray-600 focus:outline-none" 
                            type="button" aria-label="Fechar notificação"
                            onclick="window.toastSystem.remove(this.parentNode.parentNode.parentNode)">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
            ${progress}
        `;

        // ID único
        toast.id = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5);

        // Se handler for função, anexar listener
        if (options.action && typeof options.action.handler === 'function') {
            const btn = toast.querySelector('.toast-action-btn');
            if (btn) btn.addEventListener('click', options.action.handler);
        }

        return toast;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    getDurationByType(type) {
        const durations = {
            'error': 3000,    // 3 segundos para erros
            'warning': 2500,  // 2.5 segundos para avisos
            'success': 2000,  // 2 segundos para sucesso
            'info': 2000      // 2 segundos para info
        };
        return durations[type] || this.defaultDuration;
    }

    enforceLimits() {
        while (this.container.children.length >= this.maxToasts) {
            const oldest = this.container.firstElementChild;
            if (oldest) this.remove(oldest);
        }
    }

    remove(toast) {
        if (!toast || !toast.parentNode) return;

        // Animação de saída
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    clear() {
        Array.from(this.container.children).forEach(toast => this.remove(toast));
    }

    getToastConfig(type) {
        // Sistema simplificado de caminhos
        const basePath = (typeof window.getBasePath === 'function') ? window.getBasePath() : (window.location.pathname.includes('/public/') ? '' : '/Honra-e-Sombra/public/');

        const configs = {
            success: {
                bg: 'bg-green-50',
                border: 'border-green-400',
                text: 'text-green-800',
                titleColor: 'text-green-800',
                messageColor: 'text-green-700',
                actionColor: 'text-green-600',
                closeColor: 'text-green-400',
                closeHover: 'text-green-600',
                focusRing: 'focus:ring-green-500',
                progressBar: 'bg-green-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/trophy.svg" alt="Sucesso" class="h-6 w-6 icon-secondary">`
            },
            error: {
                bg: 'bg-red-50',
                border: 'border-red-400',
                text: 'text-red-800',
                titleColor: 'text-red-800',
                messageColor: 'text-red-700',
                actionColor: 'text-red-600',
                closeColor: 'text-red-400',
                closeHover: 'text-red-600',
                focusRing: 'focus:ring-red-500',
                progressBar: 'bg-red-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/skull-crossed-bones.svg" alt="Erro" class="h-6 w-6 icon-accent">`
            },
            warning: {
                bg: 'bg-yellow-50',
                border: 'border-yellow-400',
                text: 'text-yellow-800',
                titleColor: 'text-yellow-800',
                messageColor: 'text-yellow-700',
                actionColor: 'text-yellow-600',
                closeColor: 'text-yellow-400',
                closeHover: 'text-yellow-600',
                focusRing: 'focus:ring-yellow-500',
                progressBar: 'bg-yellow-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/fire-ring.svg" alt="Aviso" class="h-6 w-6 icon-primary">`
            },
            info: {
                bg: 'bg-blue-50',
                border: 'border-blue-400',
                text: 'text-blue-800',
                titleColor: 'text-blue-800',
                messageColor: 'text-blue-700',
                actionColor: 'text-blue-600',
                closeColor: 'text-blue-400',
                closeHover: 'text-blue-600',
                focusRing: 'focus:ring-blue-500',
                progressBar: 'bg-blue-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/scroll-unfurled.svg" alt="Info" class="h-6 w-6 icon-purple">`
            }
        };

        return configs[type] || configs.info;
    }

    remove(toast) {
        if (!toast || !toast.parentNode) return;

        // Animação de saída
        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-full', 'opacity-0');

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    removeOldestToast() {
        const oldestToast = this.container.firstElementChild;
        if (oldestToast) {
            this.remove(oldestToast);
        }
    }

    clear() {
        Array.from(this.container.children).forEach(toast => this.remove(toast));
    }
}

// Inicializar o sistema quando a página carregar
document.addEventListener('DOMContentLoaded', function () {
    window.toastSystem = new ToastSystem();
});

// Expor métodos globais para facilitar o uso
window.toast = {
    success: (message, options = {}) => window.toastSystem?.show(message, 'success', 2000, options),
    error: (message, options = {}) => window.toastSystem?.show(message, 'error', 3000, options),
    warning: (message, options = {}) => window.toastSystem?.show(message, 'warning', 2500, options),
    info: (message, options = {}) => window.toastSystem?.show(message, 'info', 2000, options),
    clear: () => window.toastSystem?.clear()
};
