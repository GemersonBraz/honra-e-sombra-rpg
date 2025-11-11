// Toast System - Versão Funcional baseada no debug
// Sistema Honra e Sombra
// Melhorias: acessibilidade, basePath utilitário, remoção de CSS inline injetado,
// suporte a action.handler como função e padronização de durações por tipo.

class ToastSystem {
    constructor() {
        this.debug = false;
        this.container = this.createContainer();
        document.body.appendChild(this.container);
        this.debug && console.log('Toast System inicializado');
    }

    createContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            width: 100%;
            pointer-events: none;
        `;
        // Acessibilidade: região de mensagens não intrusivas
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'false');
        return container;
    }

    show(message, type = 'info', duration = null, options = {}) {
        // Usar duração específica do tipo se não for especificada
        const finalDuration = duration || this.getDurationByType(type);

        this.debug && console.log('Criando toast:', { message, type, duration: finalDuration });

        const toast = document.createElement('div');
        const config = this.getToastConfig(type);

        // Configuração básica do toast
        toast.style.cssText = `
            width: 100%;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease-out;
            pointer-events: auto;
            cursor: default;
        `;
        toast.setAttribute('role', 'alert');

        toast.className = `
            ${config.bg} ${config.border} ${config.text}
            border-l-4 rounded-lg shadow-lg p-4 
            hover:shadow-xl transition-shadow duration-200
        `.replace(/\s+/g, ' ').trim();

        // HTML estruturado
        const title = options.title ? `<div class="font-semibold text-sm ${config.titleColor} mb-1">${this.escapeHtml(options.title)}</div>` : '';
        // Mantém compatibilidade com handler string; se for função, anexaremos ouvinte após inserir no DOM
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
                     style="width: 100%; animation: progressShrink ${finalDuration / 1000}s linear forwards;"></div>
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

        this.container.appendChild(toast);

        // Se handler de ação for função, anexar listener com segurança
        if (options.action && typeof options.action.handler === 'function') {
            const btn = toast.querySelector('.toast-action-btn');
            if (btn) btn.addEventListener('click', options.action.handler);
        }

        // Mostrar toast com animação (mais confiável com rAF)
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        });

        // Auto remover após duração especificada
        setTimeout(() => {
            this.remove(toast);
        }, finalDuration);

        return toast;
    }

    getToastConfig(type) {
        // Sistema simplificado de caminhos (pelo utilitário global)
        const basePath = (typeof window.getBasePath === 'function') ? window.getBasePath() : this.computeBasePath();

        const configs = {
            success: {
                bg: 'bg-green-50',
                border: 'border-green-400',
                text: 'text-green-800',
                titleColor: 'text-green-800',
                messageColor: 'text-green-700',
                actionColor: 'text-green-600',
                progressBar: 'bg-green-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/trophy.svg" alt="Sucesso" class="h-6 w-6 icon-success">`
            },
            error: {
                bg: 'bg-red-50',
                border: 'border-red-400',
                text: 'text-red-800',
                titleColor: 'text-red-800',
                messageColor: 'text-red-700',
                actionColor: 'text-red-600',
                progressBar: 'bg-red-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/skull-crossed-bones.svg" alt="Erro" class="h-6 w-6 icon-error">`
            },
            warning: {
                bg: 'bg-yellow-50',
                border: 'border-yellow-400',
                text: 'text-yellow-800',
                titleColor: 'text-yellow-800',
                messageColor: 'text-yellow-700',
                actionColor: 'text-yellow-600',
                progressBar: 'bg-yellow-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/fire-ring.svg" alt="Aviso" class="h-6 w-6 icon-warning">`
            },
            info: {
                bg: 'bg-blue-50',
                border: 'border-blue-400',
                text: 'text-blue-800',
                titleColor: 'text-blue-800',
                messageColor: 'text-blue-700',
                actionColor: 'text-blue-600',
                progressBar: 'bg-blue-400',
                icon: `<img src="${basePath}img/icons-1x1/lorc/scroll-unfurled.svg" alt="Info" class="h-6 w-6 icon-info">`
            }
        };

        return configs[type] || configs.info;
    }

    getDurationByType(type) {
        const durations = {
            success: 2000,
            info: 2000,
            warning: 2500,
            error: 3000
        };
        return durations[type] || 2000;
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

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Utilitário global de basePath (idempotente)
if (typeof window.getBasePath !== 'function') {
    window.getBasePath = function () {
        const path = window.location.pathname || '';
        return path.includes('/public/') ? '' : '/Honra-e-Sombra/public/';
    };
}

// Fallback local se utilitário não existir por algum motivo
ToastSystem.prototype.computeBasePath = function () {
    const path = window.location.pathname || '';
    return path.includes('/public/') ? '' : '/Honra-e-Sombra/public/';
};

// Inicializar o sistema quando a página carregar
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar sistema de toasts
    window.toastSystem = new ToastSystem();

    window.toastSystem.debug && console.log('Toast system carregado completamente');
});

// Expor métodos globais para facilitar o uso
window.toast = {
    success: (message, options = {}) => window.toastSystem?.show(message, 'success', null, options),
    error: (message, options = {}) => window.toastSystem?.show(message, 'error', null, options),
    warning: (message, options = {}) => window.toastSystem?.show(message, 'warning', null, options),
    info: (message, options = {}) => window.toastSystem?.show(message, 'info', null, options),
    clear: () => window.toastSystem?.clear()
};

// Log condicionado ao modo debug
// console.log('Toast system definido globalmente');