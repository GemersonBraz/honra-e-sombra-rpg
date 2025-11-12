<?php
/**
 * Gerenciamento de Classes
 */

// Verificar se é admin
if (!isAdmin()) {
    redirect('/');
    exit;
}

// Incluir header
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/navbar.php';

// Conectar ao banco
$conn = getDB();

// Buscar todas as classes
$stmt = $conn->query("SELECT * FROM classes ORDER BY nome ASC");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar elementos para dropdown
$elementos = $conn->query("SELECT nome FROM elementos ORDER BY nome")->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Card de Título -->
        <div class="card border-l-4 border-blue-500 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-500/10 flex items-center justify-center">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/helmet-head-shot.svg" alt="Classes" 
                             class="w-8 h-8 icon-primary">
                    </div>
                    <div>
                        <h1 class="font-title text-2xl font-bold text-primary">Gerenciar Classes</h1>
                        <p class="text-text/70 text-sm mt-1">
                            Total de <?= count($classes) ?> classes cadastradas
                        </p>
                    </div>
                </div>
                
                <button onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/index'" 
                        class="btn-outline inline-flex items-center gap-2">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/arrow-dunk.svg" alt="Voltar" 
                         class="w-4 h-4 icon-muted rotate-180">
                    Voltar
                </button>
            </div>
        </div>
        
        <!-- Botão Adicionar Classe -->
        <div class="mb-4 flex justify-end">
            <button onclick="openCreateModal()" class="btn-primary inline-flex items-center gap-2">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/layered-armor.svg" alt="Adicionar" 
                     class="w-4 h-4 icon-white">
                Adicionar Classe
            </button>
        </div>
        
        <!-- Tabela de Classes -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="user-table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>HP</th>
                            <th>ATK</th>
                            <th>DEF</th>
                            <th>Elemento</th>
                            <th>Especialidade</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $classe): ?>
                        <tr>
                            <td class="font-mono text-sm">#<?= $classe['id'] ?></td>
                            <td>
                                <?php if ($classe['imagem']): ?>
                                    <img src="<?= SITE_URL ?>/<?= htmlspecialchars($classe['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($classe['nome']) ?>" 
                                         class="w-12 h-12 rounded-lg object-cover border-2 border-border/30">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-lg bg-muted/30 flex items-center justify-center">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/helmet-head-shot.svg" 
                                             alt="Sem imagem" 
                                             class="w-6 h-6 icon-muted opacity-30">
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="font-semibold capitalize">
                                    <?= htmlspecialchars($classe['nome']) ?>
                                </span>
                            </td>
                            <td><?= $classe['hp_base'] ?></td>
                            <td><?= $classe['atk_base'] ?></td>
                            <td><?= $classe['def_base'] ?></td>
                            <td class="capitalize">
                                <?= $classe['elemento_afinidade'] ?? '-' ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($classe['especialidade']) ?: '-' ?>
                            </td>
                            <td>
                                <?php if ($classe['ativo']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        Ativa
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-50 text-slate-700 dark:bg-slate-900/20 dark:text-slate-400 border border-slate-200 dark:border-slate-800">
                                        Inativa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='editClass(<?= json_encode($classe) ?>)' 
                                            class="p-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded transition-colors"
                                            title="Editar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/quill.svg" 
                                             alt="Editar" class="w-4 h-4 icon-primary">
                                    </button>
                                    <button onclick="confirmDelete(<?= $classe['id'] ?>, '<?= htmlspecialchars($classe['nome']) ?>')" 
                                            class="p-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded transition-colors"
                                            title="Deletar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/skull-crossed-bones.svg" 
                                             alt="Deletar" class="w-4 h-4">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-text/60 py-8">
                                Nenhuma classe cadastrada ainda.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</main>

<!-- Modal de Edição/Criação -->
<div id="classModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4" 
     style="display: none; opacity: 0; transition: opacity 0.3s ease;">
    <div class="card max-w-2xl w-full h-[600px] flex flex-col" onclick="event.stopPropagation()">
        <div class="p-4 border-b border-primary/10 flex-shrink-0">
            <h2 id="modalTitle" class="font-title text-xl font-bold text-primary">Nova Classe</h2>
        </div>
        
        <form id="classForm" class="overflow-y-auto flex-1 scrollbar-thin scrollbar-thumb-primary/20 scrollbar-track-transparent" enctype="multipart/form-data">
            <div class="p-4 space-y-3">
            <input type="hidden" id="classId" name="id">
            <input type="hidden" id="formAction" name="action" value="create">
            
            <div class="grid md:grid-cols-2 gap-4">
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Nome *</label>
                    <input type="text" id="className" name="nome" required
                           class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                                  focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
                
                <!-- Elemento Afinidade -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Elemento Afinidade</label>
                    <select id="classElemento" name="elemento_afinidade"
                            class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                                   focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Nenhum</option>
                        <?php foreach ($elementos as $elem): ?>
                            <option value="<?= htmlspecialchars($elem) ?>" class="capitalize">
                                <?= htmlspecialchars($elem) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-4">
                <!-- HP Base -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">HP Base *</label>
                    <input type="number" id="classHP" name="hp_base" required min="0"
                           class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                                  focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
                
                <!-- ATK Base -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">ATK Base *</label>
                    <input type="number" id="classATK" name="atk_base" required min="0"
                           class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                                  focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
                
                <!-- DEF Base -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">DEF Base *</label>
                    <input type="number" id="classDEF" name="def_base" required min="0"
                           class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                                  focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
            </div>
            
            <!-- Descrição -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">Descrição</label>
                <textarea id="classDesc" name="descricao" rows="3"
                          class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                                 focus:outline-none focus:ring-2 focus:ring-primary/50"></textarea>
            </div>
            
            <!-- Especialidade -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">Especialidade</label>
                <input type="text" id="classEspec" name="especialidade"
                       class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                              focus:outline-none focus:ring-2 focus:ring-primary/50"
                       placeholder="Ex: Combate Corpo a Corpo">
            </div>
            
            <!-- Bônus Especial -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">Bônus Especial</label>
                <textarea id="classBonus" name="bonus_especial" rows="2"
                          class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                                 focus:outline-none focus:ring-2 focus:ring-primary/50"></textarea>
            </div>
            
            <!-- Imagem -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">Imagem da Classe</label>
                <input type="file" id="classImagemFile" name="imagem_file" accept="image/*"
                       class="w-full px-4 py-2 rounded-lg border border-primary/20 bg-background text-text 
                              focus:outline-none focus:ring-2 focus:ring-primary/50 file:mr-4 file:py-2 file:px-4 
                              file:rounded file:border-0 file:bg-primary file:text-white hover:file:bg-primary/80">
                <input type="hidden" id="classImagem" name="imagem">
                <input type="hidden" id="removeImagem" name="remove_imagem" value="0">
                <p class="text-xs text-text/60 mt-1">
                    Formatos aceitos: JPG, PNG, GIF (max 2MB)
                </p>
                <div id="imagePreview" class="mt-2 hidden">
                    <img id="previewImg" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-primary/20">
                    <button type="button" onclick="removeImage()" class="text-xs text-red-600 hover:text-red-700 mt-1 block">
                        Remover imagem
                    </button>
                </div>
            </div>
            
            <!-- Ativo -->
            <div class="flex items-center gap-3">
                <input type="checkbox" id="classAtivo" name="ativo" value="1" checked
                       class="w-4 h-4 rounded border-primary/20 text-primary focus:ring-primary/50">
                <label for="classAtivo" class="text-sm font-semibold text-primary">Classe Ativa</label>
            </div>
            </div>
            
            <!-- Botões -->
            <div class="flex items-center gap-3 p-4 border-t border-primary/10 flex-shrink-0 bg-background">
                <button type="submit" class="btn-primary flex-1">
                    Salvar Classe
                </button>
                <button type="button" onclick="closeModal()" class="btn-outline flex-1">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4" 
     style="display: none; opacity: 0; transition: opacity 0.3s ease;">
    <div class="card max-w-md w-full" onclick="event.stopPropagation()">
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/skull-crossed-bones.svg" alt="Deletar" 
                     class="w-8 h-8">
            </div>
            
            <h3 class="font-title text-xl font-bold text-primary mb-2">Confirmar Exclusão</h3>
            <p class="text-text/70 mb-1">Tem certeza que deseja excluir a classe</p>
            <p class="font-semibold text-primary mb-4" id="deleteClassName"></p>
            <p class="text-sm text-red-600 dark:text-red-400 font-semibold">
                ⚠️ Esta ação não pode ser desfeita!
            </p>
            
            <div class="flex items-center gap-3 mt-6">
                <button onclick="closeDeleteModal()" class="btn-outline flex-1">
                    Cancelar
                </button>
                <button onclick="executeDelete()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors flex-1">
                    Deletar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteClassId = null;

// Preview de imagem
document.getElementById('classImagemFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validar tamanho (2MB)
        if (file.size > 2 * 1024 * 1024) {
            toast.error('A imagem deve ter no máximo 2MB');
            e.target.value = '';
            return;
        }
        
        // Validar tipo
        if (!file.type.startsWith('image/')) {
            toast.error('Apenas imagens são permitidas');
            e.target.value = '';
            return;
        }
        
        // Mostrar preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Remover imagem
function removeImage() {
    document.getElementById('classImagemFile').value = '';
    document.getElementById('classImagem').value = '';
    document.getElementById('removeImagem').value = '1'; // Marca para remover no servidor
    document.getElementById('imagePreview').classList.add('hidden');
}

// Abrir modal de criação
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Nova Classe';
    document.getElementById('formAction').value = 'create';
    document.getElementById('classForm').reset();
    document.getElementById('classId').value = '';
    document.getElementById('classAtivo').checked = true;
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('removeImagem').value = '0';
    
    showModal('classModal');
}

// Editar classe
function editClass(classe) {
    document.getElementById('modalTitle').textContent = 'Editar Classe';
    document.getElementById('formAction').value = 'update';
    document.getElementById('classId').value = classe.id;
    document.getElementById('className').value = classe.nome;
    document.getElementById('classHP').value = classe.hp_base;
    document.getElementById('classATK').value = classe.atk_base;
    document.getElementById('classDEF').value = classe.def_base;
    document.getElementById('classElemento').value = classe.elemento_afinidade || '';
    document.getElementById('classDesc').value = classe.descricao || '';
    document.getElementById('classEspec').value = classe.especialidade || '';
    document.getElementById('classBonus').value = classe.bonus_especial || '';
    document.getElementById('classImagem').value = classe.imagem || '';
    document.getElementById('classAtivo').checked = classe.ativo == 1;
    
    // Mostrar preview se houver imagem
    if (classe.imagem) {
        document.getElementById('previewImg').src = '<?= SITE_URL ?>/' + classe.imagem;
        document.getElementById('imagePreview').classList.remove('hidden');
    } else {
        document.getElementById('imagePreview').classList.add('hidden');
    }
    
    showModal('classModal');
}

// Mostrar modal
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
}

// Fechar modal
function closeModal() {
    const modal = document.getElementById('classModal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Confirmar exclusão
function confirmDelete(id, nome) {
    deleteClassId = id;
    document.getElementById('deleteClassName').textContent = nome;
    showModal('deleteModal');
}

// Fechar modal de exclusão
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        deleteClassId = null;
    }, 300);
}

// Executar exclusão
async function executeDelete() {
    if (!deleteClassId) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', deleteClassId);
        
        const response = await fetch('/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/classes_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            toast.success(result.message);
            closeDeleteModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(result.message);
        }
    } catch (error) {
        toast.error('Erro ao deletar classe: ' + error.message);
    }
}

// Submit do formulário
document.getElementById('classForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Garantir que o checkbox ativo sempre envie 0 ou 1
    const ativoCheckbox = document.getElementById('classAtivo');
    formData.set('ativo', ativoCheckbox.checked ? '1' : '0');
    
    try {
        const response = await fetch('/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/classes_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        console.log('Resposta do servidor:', text);
        
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            console.error('Erro ao fazer parse do JSON:', parseError);
            console.error('Texto recebido:', text);
            toast.error('Erro no servidor. Verifique o console para detalhes.');
            return;
        }
        
        if (result.success) {
            toast.success(result.message);
            closeModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(result.message);
        }
    } catch (error) {
        console.error('Erro completo:', error);
        toast.error('Erro ao salvar classe: ' + error.message);
    }
});

// Fechar modal ao clicar fora
document.getElementById('classModal').addEventListener('click', closeModal);
document.getElementById('deleteModal').addEventListener('click', closeDeleteModal);
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
