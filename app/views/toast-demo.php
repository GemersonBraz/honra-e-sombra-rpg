<?php
$pageTitle = 'Demo de Notificações';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Main Content -->
<main class="flex-1 theme-bg-background theme-transition">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="font-title text-4xl font-bold theme-text-primary mb-4">
                Sistema de Notificações
            </h1>
            <p class="font-heading text-lg theme-text-secondary max-w-2xl mx-auto">
                Demonstração completa do sistema de toasts integrado ao tema Honra e Sombra
            </p>
        </div>

        <!-- Toast Básicos -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="theme-bg-surface p-6 rounded-lg theme-transition">
                <h3 class="font-heading font-semibold theme-text-primary mb-4">Toasts Básicos</h3>
                <div class="space-y-3">
                    <button onclick="window.toast.success('Operação realizada com sucesso!')" 
                            class="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Sucesso
                    </button>
                    <button onclick="window.toast.error('Algo deu errado!')" 
                            class="w-full bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Erro
                    </button>
                    <button onclick="window.toast.warning('Atenção necessária!')" 
                            class="w-full bg-yellow-600 hover:bg-yellow-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Aviso
                    </button>
                    <button onclick="window.toast.info('Informação importante!')" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Info
                    </button>
                </div>
            </div>

            <!-- Toasts de Autenticação -->
            <div class="theme-bg-surface p-6 rounded-lg theme-transition">
                <h3 class="font-heading font-semibold theme-text-primary mb-4">Autenticação</h3>
                <div class="space-y-3">
                    <button onclick="authToasts.loginSuccess('João Silva')" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Login Sucesso
                    </button>
                    <button onclick="authToasts.loginError()" 
                            class="w-full bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Login Erro
                    </button>
                    <button onclick="authToasts.registerSuccess()" 
                            class="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Registro OK
                    </button>
                    <button onclick="authToasts.logoutSuccess()" 
                            class="w-full bg-gray-600 hover:bg-gray-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Logout
                    </button>
                </div>
            </div>

            <!-- Toasts RPG -->
            <div class="theme-bg-surface p-6 rounded-lg theme-transition">
                <h3 class="font-heading font-semibold theme-text-primary mb-4">RPG</h3>
                <div class="space-y-3">
                    <button onclick="rpgToasts.levelUp(15)" 
                            class="w-full bg-yellow-600 hover:bg-yellow-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Level Up
                    </button>
                    <button onclick="rpgToasts.characterCreated('Akira', 'Ninja')" 
                            class="w-full bg-purple-600 hover:bg-purple-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Novo Personagem
                    </button>
                    <button onclick="rpgToasts.questCompleted('O Templo Perdido')" 
                            class="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Quest Completa
                    </button>
                    <button onclick="rpgToasts.itemObtained('Katana Lendária')" 
                            class="w-full bg-orange-600 hover:bg-orange-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Item Obtido
                    </button>
                </div>
            </div>

            <!-- Toasts do Sistema -->
            <div class="theme-bg-surface p-6 rounded-lg theme-transition">
                <h3 class="font-heading font-semibold theme-text-primary mb-4">Sistema</h3>
                <div class="space-y-3">
                    <button onclick="systemToasts.featureComingSoon('Classes Avançadas')" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Em Breve
                    </button>
                    <button onclick="systemToasts.maintenance()" 
                            class="w-full bg-yellow-600 hover:bg-yellow-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Manutenção
                    </button>
                    <button onclick="systemToasts.updateAvailable('2.0')" 
                            class="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Atualização
                    </button>
                    <button onclick="systemToasts.backupComplete()" 
                            class="w-full bg-gray-600 hover:bg-gray-500 text-white py-2 px-4 rounded text-sm font-heading">
                        Backup
                    </button>
                </div>
            </div>
        </div>

        <!-- Exemplos Avançados -->
        <div class="theme-bg-surface rounded-lg p-8 mb-8 theme-transition">
            <h3 class="font-heading text-xl font-semibold theme-text-primary mb-6">Exemplos Avançados</h3>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-heading font-medium theme-text-primary mb-4">Com Ações</h4>
                    <div class="space-y-3">
                        <button onclick="showToastWithAction()" 
                                class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded font-heading">
                            Toast com Botão
                        </button>
                        <button onclick="showConfirmationToast()" 
                                class="w-full bg-yellow-600 hover:bg-yellow-500 text-white py-2 px-4 rounded font-heading">
                            Confirmação
                        </button>
                        <button onclick="showPersistentToast()" 
                                class="w-full bg-red-600 hover:bg-red-500 text-white py-2 px-4 rounded font-heading">
                            Persistente
                        </button>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-heading font-medium theme-text-primary mb-4">Durações Diferentes</h4>
                    <div class="space-y-3">
                        <button onclick="window.toast.info('Mensagem rápida!', {duration: 1000})" 
                                class="w-full bg-gray-600 hover:bg-gray-500 text-white py-2 px-4 rounded font-heading">
                            Rápido (1s)
                        </button>
                        <button onclick="window.toast.success('Mensagem normal!', {duration: 2000})" 
                                class="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded font-heading">
                            Normal (2s)
                        </button>
                        <button onclick="window.toast.warning('Mensagem lenta!', {duration: 5000})" 
                                class="w-full bg-yellow-600 hover:bg-yellow-500 text-white py-2 px-4 rounded font-heading">
                            Lento (5s)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Código de Exemplo -->
        <div class="theme-bg-surface rounded-lg p-8 theme-transition">
            <h3 class="font-heading text-xl font-semibold theme-text-primary mb-6">Como Usar</h3>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-heading font-medium theme-text-primary mb-4">JavaScript Básico</h4>
                    <div class="bg-gray-900 text-green-400 p-4 rounded-lg text-sm font-mono">
                        <div>// Toasts básicos</div>
                        <div>toast.success('Sucesso!');</div>
                        <div>toast.error('Erro!');</div>
                        <div>toast.warning('Aviso!');</div>
                        <div>toast.info('Info!');</div>
                        <div class="mt-3">// Com opções</div>
                        <div>toast.success('Mensagem', {</div>
                        <div>&nbsp;&nbsp;duration: 5000,</div>
                        <div>&nbsp;&nbsp;title: 'Título',</div>
                        <div>&nbsp;&nbsp;action: {</div>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;text: 'Ação',</div>
                        <div>&nbsp;&nbsp;&nbsp;&nbsp;handler: 'alert("Ok!")'</div>
                        <div>&nbsp;&nbsp;}</div>
                        <div>});</div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-heading font-medium theme-text-primary mb-4">Funções Helper</h4>
                    <div class="bg-gray-900 text-blue-400 p-4 rounded-lg text-sm font-mono">
                        <div>// Autenticação</div>
                        <div>authToasts.loginSuccess();</div>
                        <div>authToasts.registerSuccess();</div>
                        <div class="mt-3">// RPG</div>
                        <div>rpgToasts.levelUp(15);</div>
                        <div>rpgToasts.questCompleted();</div>
                        <div class="mt-3">// Sistema</div>
                        <div>systemToasts.featureComingSoon();</div>
                        <div>systemToasts.maintenance();</div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<script>
// Exemplos de toasts avançados
function showToastWithAction() {
    window.toast.info('Nova versão disponível! Deseja atualizar?', {
        title: 'Atualização',
        action: {
            text: 'Atualizar',
            handler: 'alert("Atualizando sistema...")'
        },
        duration: 4000
    });
}

function showConfirmationToast() {
    window.toast.warning('Tem certeza que deseja excluir este personagem?', {
        title: 'Confirmação',
        action: {
            text: 'Confirmar',
            handler: 'toast.error("Personagem excluído!")'
        },
        duration: 5000
    });
}

function showPersistentToast() {
    window.toast.error('Erro crítico no sistema! Contate o administrador.', {
        title: 'Erro Crítico',
        persistent: true,
        action: {
            text: 'Entendi',
            handler: ''
        }
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>