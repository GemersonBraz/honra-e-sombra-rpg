/**
 * Funções auxiliares para toasts específicos do sistema Honra e Sombra
 */

// Toasts para autenticação
window.authToasts = {
    loginSuccess: (username = '') => {
        toast.success(
            `Bem-vindo${username ? ', ' + username : ''}! Login realizado com sucesso.`,
            {
                title: 'Login Realizado'
            }
        );
    },

    loginError: (message = 'Credenciais inválidas') => {
        toast.error(message, {
            title: 'Erro no Login'
        });
    },

    registerSuccess: () => {
        toast.success(
            'Conta criada com sucesso! Agora você pode fazer login.',
            {
                title: 'Conta Criada'
            }
        );
    },

    logoutSuccess: () => {
        toast.info(
            'Você foi desconectado com segurança. Até a próxima aventura!',
            { title: 'Logout Realizado' }
        );
    },

    sessionExpired: () => {
        toast.warning(
            'Sua sessão expirou. Faça login novamente para continuar.',
            {
                title: 'Sessão Expirada'
            }
        );
    }
};

// Toasts para personagens (para futuras implementações)
window.characterToasts = {
    created: (name) => {
        toast.success(
            `Personagem "${name}" foi criado com sucesso!`,
            {
                title: 'Novo Personagem',
                action: {
                    text: 'Ver Personagem',
                    handler: 'window.location.href="index.php?page=personagens"'
                }
            }
        );
    },

    updated: (name) => {
        toast.success(
            `Personagem "${name}" foi atualizado.`,
            { title: 'Personagem Atualizado' }
        );
    },

    deleted: (name) => {
        toast.info(
            `Personagem "${name}" foi removido.`,
            { title: 'Personagem Removido' }
        );
    },

    levelUp: (name, level) => {
        toast.success(
            `${name} subiu para o nível ${level}! Parabéns!`,
            {
                title: 'Level Up!',
                progress: false
            }
        );
    }
};

// Toasts para sistema geral
window.systemToasts = {
    saved: () => {
        toast.success('Dados salvos com sucesso!', { title: 'Salvo' });
    },

    loading: () => {
        toast.info('Carregando...', {
            title: 'Aguarde',
            progress: false
        });
    },

    error: (message = 'Ocorreu um erro inesperado') => {
        toast.error(message, {
            title: 'Erro',
            action: {
                text: 'Reportar Erro',
                handler: 'window.open("mailto:suporte@honrasombra.com?subject=Erro%20no%20Sistema", "_blank")'
            }
        });
    },

    maintenance: () => {
        toast.warning(
            'O sistema estará em manutenção programada para melhorias.',
            {
                title: 'Manutenção Programada',
                action: {
                    text: 'Mais Info',
                    handler: 'alert("Manutenção programada para implementação de novas funcionalidades")'
                }
            }
        );
    },

    updateAvailable: (version) => {
        toast.info(
            `Nova versão ${version} disponível! Clique para atualizar.`,
            {
                title: 'Atualização Disponível',
                action: {
                    text: 'Atualizar',
                    handler: 'alert("Simulando atualização do sistema...")'
                }
            }
        );
    },

    backupComplete: () => {
        toast.success(
            'Backup do sistema realizado com sucesso.',
            {
                title: 'Backup Completo',
                action: {
                    text: 'Ver Detalhes',
                    handler: 'alert("Backup realizado às " + new Date().toLocaleTimeString())'
                }
            }
        );
    },

    featureComingSoon: (feature = 'Esta funcionalidade') => {
        toast.info(
            `${feature} será implementada em breve!`,
            {
                title: 'Em Desenvolvimento',
                action: {
                    text: 'Ver Roadmap',
                    handler: 'alert("Consulte o README.md para ver o roadmap completo")'
                }
            }
        );
    },

    permissionDenied: () => {
        toast.error(
            'Você não tem permissão para acessar esta área.',
            {
                title: 'Acesso Negado',
                action: {
                    text: 'Voltar',
                    handler: 'history.back()'
                }
            }
        );
    }
};

// Toasts para validação de formulários
window.formToasts = {
    validationError: (field) => {
        toast.warning(
            `Por favor, verifique o campo: ${field}`,
            { title: 'Erro de Validação' }
        );
    },

    requiredFields: () => {
        toast.warning(
            'Preencha todos os campos obrigatórios.',
            { title: 'Campos Obrigatórios' }
        );
    },

    passwordMismatch: () => {
        toast.error(
            'As senhas não coincidem. Tente novamente.',
            { title: 'Senhas Diferentes' }
        );
    },

    weakPassword: () => {
        toast.warning(
            'Sua senha é muito fraca. Use pelo menos 8 caracteres.',
            { title: 'Senha Fraca' }
        );
    },

    invalidEmail: () => {
        toast.error(
            'O formato do email não é válido.',
            { title: 'Email Inválido' }
        );
    }
};

// Toasts com tema RPG
window.rpgToasts = {
    questCompleted: (questName) => {
        toast.success(
            `Missão "${questName}" concluída! XP e recompensas recebidas.`,
            {
                title: 'Missão Concluída'
            }
        );
    },

    levelUp: (level) => {
        toast.success(
            `Parabéns! Você alcançou o nível ${level}!`,
            {
                title: 'Level Up'
            }
        );
    },

    characterCreated: (name, classe) => {
        toast.success(
            `Personagem "${name}" (${classe}) criado com sucesso!`,
            {
                title: 'Personagem Criado'
            }
        );
    },

    itemObtained: (itemName) => {
        toast.success(
            `Novo item obtido: ${itemName}!`,
            {
                title: 'Item Obtido'
            }
        );
    },

    skillLearned: (skillName) => {
        toast.info(
            `Nova habilidade aprendida: ${skillName}`,
            { title: 'Habilidade Desbloqueada' }
        );
    },

    itemReceived: (itemName) => {
        toast.success(
            `Item recebido: ${itemName}`,
            { title: 'Novo Item' }
        );
    },

    battleWon: () => {
        toast.success(
            'Vitória! Você derrotou seus inimigos com honra.',
            { title: 'Batalha Vencida' }
        );
    },

    battleLost: () => {
        toast.error(
            'Derrota... Mas um verdadeiro guerreiro sempre se levanta.',
            {
                title: 'Batalha Perdida',
                action: {
                    text: 'Tentar Novamente',
                    handler: 'location.reload()'
                }
            }
        );
    }
};

// Função para converter mensagens PHP em toasts
window.convertPhpMessage = function () {
    // Procurar por mensagens PHP existentes e convertê-las em toasts
    const messageElements = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');

    messageElements.forEach(element => {
        const isSuccess = element.className.includes('bg-green-100');
        const message = element.textContent.trim();

        if (message) {
            if (isSuccess) {
                toast.success(message);
            } else {
                toast.error(message);
            }

            // Remover a mensagem PHP original
            element.style.display = 'none';
        }
    });
};

// Auto-converter mensagens PHP quando a página carregar
document.addEventListener('DOMContentLoaded', function () {
    // Aguardar um pouco para garantir que o sistema de toast esteja carregado
    setTimeout(convertPhpMessage, 100);
});
