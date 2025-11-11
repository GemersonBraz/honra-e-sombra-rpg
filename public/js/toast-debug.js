// Toast System Debug - Versão Simplificada

class ToastSystem {
    constructor() {
        this.container = this.createContainer();
        document.body.appendChild(this.container);
        console.log('Toast System inicializado');
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
        return container;
    }

    show(message, type = 'info', duration = 2000, options = {}) {
        console.log('Criando toast:', {message, type, duration});
        
        const toast = document.createElement('div');
        toast.style.cssText = `
            background: white;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 12px;
            padding: 16px;
            position: relative;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            pointer-events: auto;
        `;

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        toast.style.borderLeftColor = colors[type] || colors.info;

        // HTML simples
        toast.innerHTML = `
            <div style="display: flex; align-items: start;">
                <div style="flex: 1;">
                    <div style="color: #374151; font-weight: 500;">${message}</div>
                </div>
                <button onclick="window.toastSystem.remove(this.parentNode.parentNode)" 
                        style="margin-left: 12px; color: #6b7280; cursor: pointer; background: none; border: none;">
                    ✕
                </button>
            </div>
            <div style="margin-top: 8px; background: #e5e7eb; height: 2px; border-radius: 2px; overflow: hidden;">
                <div style="background: ${colors[type] || colors.info}; height: 100%; width: 100%; animation: progressShrink ${duration/1000}s linear forwards;"></div>
            </div>
        `;

        this.container.appendChild(toast);

        // Mostrar toast
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        }, 50);

        // Auto remover
        setTimeout(() => {
            this.remove(toast);
        }, duration);

        return toast;
    }

    remove(toast) {
        if (!toast || !toast.parentNode) return;
        
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
}

// CSS para animação
const style = document.createElement('style');
style.textContent = `
@keyframes progressShrink {
    from { width: 100%; }
    to { width: 0%; }
}
`;
document.head.appendChild(style);

// Inicializar sistema
window.toastSystem = new ToastSystem();

// Métodos globais simples
window.toast = {
    success: (message) => window.toastSystem.show(message, 'success'),
    error: (message) => window.toastSystem.show(message, 'error'),
    warning: (message) => window.toastSystem.show(message, 'warning'),
    info: (message) => window.toastSystem.show(message, 'info'),
    clear: () => window.toastSystem.clear()
};

console.log('Toast system debug carregado');