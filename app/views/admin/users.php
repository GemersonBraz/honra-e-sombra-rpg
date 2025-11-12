<?php
$pageTitle = 'Gerenciar Usuários';
include __DIR__ . '/../../includes/header.php';
include __DIR__ . '/../../includes/navbar.php';

$user = new User();
$users = $user->getAllUsers();
$currentUserId = $_SESSION['user_id'];
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="card border-l-4 border-primary p-6 mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="font-title text-3xl font-bold text-primary flex items-center gap-3">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/crowned-skull.svg" alt="Usuários" class="w-8 h-8 icon-primary">
                        Gerenciar Usuários
                    </h1>
                    <p class="font-heading text-text/70 mt-2">
                        Total de <span class="font-semibold text-primary"><?= count($users) ?></span> usuários cadastrados
                    </p>
                </div>
                
                <div class="flex gap-3">
                    <a href="index.php?page=perfil" class="btn-secondary inline-flex items-center gap-2">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/quill-ink.svg" alt="Perfil" class="w-4 h-4 icon-white">
                        Meu Perfil
                    </a>
                    <a href="index.php?page=admin" class="btn-outline inline-flex items-center gap-2">
                        <img src="<?= $basePath ?>img/icons-1x1/lorc/wooden-door.svg" alt="Voltar" class="w-4 h-4 icon-muted">
                        Voltar ao Dashboard
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Tabela de Usuários -->
        <div class="card p-6">
            <h3 class="font-heading text-lg font-semibold text-primary mb-4 flex items-center gap-2">
                <img src="<?= $basePath ?>img/icons-1x1/lorc/scroll-unfurled.svg" alt="Lista" class="w-5 h-5 icon-primary">
                Lista de Usuários
            </h3>
            
            <div class="overflow-x-auto">
                <table class="user-table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="font-mono text-sm">#<?= $u['id'] ?></td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <?php if (!empty($u['avatar'])): ?>
                                        <img src="<?= $basePath . $u['avatar'] ?>" alt="Avatar" class="w-8 h-8 rounded-full border border-border object-cover">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="text-xs font-bold text-primary"><?= strtoupper(substr($u['nome'], 0, 1)) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <span class="font-medium"><?= htmlspecialchars($u['nome']) ?></span>
                                </div>
                            </td>
                            <td class="text-sm text-text/70"><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?php if ($u['tipo'] === 'admin'): ?>
                                    <span class="badge badge-primary">Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Player</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($u['ativo']): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge badge-error">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm text-text/60"><?= date('d/m/Y', strtotime($u['data_criacao'])) ?></td>
                            <td>
                                <button onclick="openUserModal(<?= $u['id'] ?>)" class="btn-sm btn-primary" title="Ver detalhes">
                                    <img src="<?= $basePath ?>img/icons-1x1/lorc/eye-shield.svg" alt="Ver" class="w-4 h-4 icon-white">
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</main>

<!-- Modal de Detalhes do Usuário -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="font-title text-2xl font-bold text-primary flex items-center gap-2">
                <img src="<?= $basePath ?>img/icons-1x1/lorc/crowned-skull.svg" alt="Usuário" class="w-6 h-6 icon-primary">
                Detalhes do Usuário
            </h2>
            <button onclick="closeUserModal()" class="modal-close">&times;</button>
        </div>
        
        <div id="modalBody" class="modal-body">
            <!-- Conteúdo será preenchido via JavaScript -->
        </div>
    </div>
</div>

<!-- Dados dos usuários em JSON para JavaScript -->
<script>
const usersData = <?= json_encode($users) ?>;
const basePath = '<?= $basePath ?>';
const currentUserId = <?= $currentUserId ?>;

function openUserModal(userId) {
    const user = usersData.find(u => u.id == userId);
    if (!user) return;
    
    const isCurrentUser = user.id == currentUserId;
    const avatarHtml = user.avatar 
        ? `<img src="${basePath}${user.avatar}" alt="Avatar" class="w-20 h-20 rounded-full border-2 border-border object-cover">`
        : `<div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center"><span class="text-2xl font-bold text-primary">${user.nome.charAt(0).toUpperCase()}</span></div>`;
    
    const statusBadge = user.ativo 
        ? '<span class="badge badge-success">Ativo</span>'
        : '<span class="badge badge-error">Inativo</span>';
    
    const tipoBadge = user.tipo === 'admin'
        ? '<span class="badge badge-primary">Admin</span>'
        : '<span class="badge badge-secondary">Player</span>';
    
    const html = `
        <div class="space-y-6">
            <!-- Avatar e Info Básica -->
            <div class="flex items-start gap-6 pb-6 border-b border-border">
                ${avatarHtml}
                <div class="flex-1">
                    <h3 class="font-heading text-xl font-bold text-primary mb-2">${escapeHtml(user.nome)}</h3>
                    ${user.display_title ? `<p class="text-text/70 text-sm mb-2">Nome de exibição: <span class="font-medium">${escapeHtml(user.display_title)}</span></p>` : ''}
                    <p class="text-text/70 text-sm mb-3">${escapeHtml(user.email)}</p>
                    <div class="flex gap-2">
                        ${tipoBadge}
                        ${statusBadge}
                    </div>
                </div>
            </div>
            
            <!-- Informações Detalhadas -->
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-text/60 mb-1">ID do Usuário</p>
                    <p class="font-mono text-sm font-medium">#${user.id}</p>
                </div>
                <div>
                    <p class="text-xs text-text/60 mb-1">Tipo de Conta</p>
                    <p class="font-medium">${user.tipo === 'admin' ? 'Administrador' : 'Jogador'}</p>
                </div>
                <div>
                    <p class="text-xs text-text/60 mb-1">Data de Cadastro</p>
                    <p class="font-medium">${formatDate(user.data_criacao)}</p>
                </div>
                <div>
                    <p class="text-xs text-text/60 mb-1">Último Login</p>
                    <p class="font-medium">${user.ultimo_login ? formatDate(user.ultimo_login) : 'Nunca'}</p>
                </div>
            </div>
            
            ${!isCurrentUser ? `
            <!-- Formulário de Edição -->
            <form method="post" action="index.php?page=admin/users" class="space-y-4 pt-6 border-t border-border">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="user_id" value="${user.id}">
                
                <h4 class="font-heading text-lg font-semibold text-primary mb-4">Editar Usuário</h4>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-heading mb-1">Nome</label>
                        <input type="text" name="nome" value="${escapeHtml(user.nome)}" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-heading mb-1">Email</label>
                        <input type="email" name="email" value="${escapeHtml(user.email)}" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-heading mb-1">Tipo</label>
                        <select name="tipo" class="form-input">
                            <option value="player" ${user.tipo === 'player' ? 'selected' : ''}>Jogador</option>
                            <option value="admin" ${user.tipo === 'admin' ? 'selected' : ''}>Administrador</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-heading mb-1">Status</label>
                        <select name="ativo" class="form-input">
                            <option value="1" ${user.ativo ? 'selected' : ''}>Ativo</option>
                            <option value="0" ${!user.ativo ? 'selected' : ''}>Inativo</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2">
                        <img src="${basePath}img/icons-1x1/lorc/checked-shield.svg" alt="Salvar" class="w-4 h-4 icon-white">
                        Salvar Alterações
                    </button>
                    <button type="button" onclick="showResetPasswordForm(${user.id})" class="btn-secondary inline-flex items-center gap-2">
                        <img src="${basePath}img/icons-1x1/lorc/key.svg" alt="Senha" class="w-4 h-4 icon-white">
                        Resetar Senha
                    </button>
                    <button type="button" onclick="confirmDelete(${user.id}, '${escapeHtml(user.nome)}')" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg font-heading text-sm inline-flex items-center gap-2">
                        <img src="${basePath}img/icons-1x1/lorc/skull-crossed-bones.svg" alt="Deletar" class="w-4 h-4 icon-white">
                        Deletar Usuário
                    </button>
                </div>
            </form>
            ` : `
            <div class="pt-6 border-t border-border">
                <p class="text-text/70 text-sm mb-4">
                    <img src="${basePath}img/icons-1x1/lorc/info.svg" alt="Info" class="w-4 h-4 inline-block mr-1 icon-muted">
                    Este é seu próprio usuário. Use o botão "Meu Perfil" para editar suas informações.
                </p>
            </div>
            `}
            
            <!-- Botão Fechar -->
            <div class="pt-4 border-t border-border">
                <button onclick="closeUserModal()" class="btn-outline w-full">
                    Fechar
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('userModal').classList.add('show');
}

function closeUserModal() {
    document.getElementById('userModal').classList.remove('show');
}

function showResetPasswordForm(userId) {
    const html = `
        <form method="post" action="index.php?page=admin/users" class="space-y-4">
            <input type="hidden" name="action" value="reset_password">
            <input type="hidden" name="user_id" value="${userId}">
            
            <h4 class="font-heading text-lg font-semibold text-primary mb-4">Resetar Senha</h4>
            
            <div>
                <label class="block text-sm font-heading mb-1">Nova Senha (mínimo 6 caracteres)</label>
                <input type="password" name="new_password" class="form-input" required minlength="6">
            </div>
            
            <div>
                <label class="block text-sm font-heading mb-1">Confirmar Nova Senha</label>
                <input type="password" name="confirm_password" class="form-input" required minlength="6">
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Resetar Senha</button>
                <button type="button" onclick="openUserModal(${userId})" class="btn-outline">Cancelar</button>
            </div>
        </form>
    `;
    
    document.getElementById('modalBody').innerHTML = html;
}

function confirmDelete(userId, userName) {
    if (confirm(`Tem certeza que deseja DELETAR o usuário "${userName}"?\n\nEsta ação marcará o usuário como inativo.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?page=admin/users';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';
        
        const userIdInput = document.createElement('input');
        userIdInput.type = 'hidden';
        userIdInput.name = 'user_id';
        userIdInput.value = userId;
        
        form.appendChild(actionInput);
        form.appendChild(userIdInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        closeUserModal();
    }
}
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>