<?php
/**
 * Gerenciamento de Elementos
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

// Parâmetros de paginação e filtros
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Filtros
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'id';
$orderDir = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

// Construir query com filtros
$where = [];
$params = [];

if ($searchTerm) {
    $where[] = "(nome LIKE :search OR descricao LIKE :search)";
    $params[':search'] = "%{$searchTerm}%";
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Validar coluna de ordenação
$allowedColumns = ['id', 'nome', 'bonus_dano_percentual'];
if (!in_array($orderBy, $allowedColumns)) {
    $orderBy = 'id';
}

// Contar total de elementos
$countSql = "SELECT COUNT(*) FROM elementos $whereClause";
$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$totalElements = $countStmt->fetchColumn();
$totalPages = ceil($totalElements / $perPage);

// Buscar elementos com paginação
$sql = "SELECT * FROM elementos $whereClause ORDER BY $orderBy $orderDir LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$elementos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Cabeçalho -->
        <div class="card border-l-4 border-orange-500 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/fire-bowl.svg" alt="Elementos" 
                         class="w-12 h-12 icon-primary">
                    <div>
                        <h1 class="font-title text-3xl font-bold text-primary mb-1">Gerenciamento de Elementos</h1>
                        <p class="text-text/70">Gerencie os elementos do jogo e suas interações</p>
                    </div>
                </div>
                <div>
                    <button onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg'" 
                            class="btn-outline inline-flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/arrow-dunk.svg" alt="Voltar" 
                             class="w-4 h-4 icon-muted rotate-180">
                        Voltar
                    </button>
                </div>
            </div>
        </div>

        
        <!-- Filtros e Busca -->
        <div class="card p-4 mb-4">
            <form method="GET" action="" class="grid md:grid-cols-3 gap-4">
                <input type="hidden" name="page" value="admin/content-management-rpg/elementos">
                
                <!-- Busca por nome/descrição -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/magnifying-glass.svg" alt="Buscar" class="w-4 h-4 icon-primary">
                        Buscar
                    </label>
                    <input type="text" 
                           name="search" 
                           value="<?= htmlspecialchars($searchTerm) ?>"
                           placeholder="Nome ou descrição..."
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text placeholder:text-text/40 focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                </div>
                
                <!-- Ordenação -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/stairs.svg" alt="Ordenar" class="w-4 h-4 icon-primary">
                        Ordenar por
                    </label>
                    <select name="order" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors" onchange="this.form.submit()">
                        <option value="id" <?= $orderBy === 'id' ? 'selected' : '' ?>>ID</option>
                        <option value="nome" <?= $orderBy === 'nome' ? 'selected' : '' ?>>Nome</option>
                        <option value="bonus_dano_percentual" <?= $orderBy === 'bonus_dano_percentual' ? 'selected' : '' ?>>Bônus de Dano</option>
                    </select>
                </div>
                
                <!-- Botões -->
                <div class="flex gap-2 items-end">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 flex-1">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/magnifying-glass.svg" alt="Filtrar" class="w-4 h-4 icon-white">
                        Aplicar Filtros
                    </button>
                    <a href="<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/elementos" 
                       class="btn-outline inline-flex items-center gap-2 flex-1">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/cancel.svg" alt="Limpar" class="w-4 h-4 icon-muted">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Botão Adicionar Elemento -->
        <div class="mb-4 flex justify-end">
            <button onclick="openCreateModal()" class="btn-primary inline-flex items-center gap-2">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/fire-bowl.svg" alt="Adicionar" 
                     class="w-4 h-4 icon-white">
                Adicionar Elemento
            </button>
        </div>
        
        <!-- Tabela de Elementos -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-amber-400/50 dark:border-amber-600/30">
                            <th class="text-center">ID</th>
                            <th class="text-center">Nome</th>
                            <th class="text-center">Cor</th>
                            <th class="text-center">Forte Contra</th>
                            <th class="text-center">Fraco Contra</th>
                            <th class="text-center">Bônus de Dano</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($elementos as $elemento): ?>
                        <tr class="border-b-2 border-amber-400/50 dark:border-amber-600/30">
                            <td class="font-mono text-sm text-center">#<?= $elemento['id'] ?></td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="font-semibold capitalize" style="color: <?= htmlspecialchars($elemento['cor_hex'] ?? '#666') ?>">
                                        <?= htmlspecialchars($elemento['nome']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-6 h-6 rounded-full border-2 border-border/30" 
                                         style="background-color: <?= htmlspecialchars($elemento['cor_hex'] ?? '#666') ?>"></div>
                                    <code class="text-xs"><?= htmlspecialchars($elemento['cor_hex'] ?? 'N/A') ?></code>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if ($elemento['forte_contra']): ?>
                                    <span class="text-xs capitalize text-success">
                                        <?= str_replace(',', ', ', htmlspecialchars($elemento['forte_contra'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-text/30">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($elemento['fraco_contra']): ?>
                                    <span class="text-xs capitalize text-danger">
                                        <?= str_replace(',', ', ', htmlspecialchars($elemento['fraco_contra'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-text/30">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="font-mono text-sm font-semibold text-primary">
                                    +<?= $elemento['bonus_dano_percentual'] ?>%
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='editElemento(<?= json_encode($elemento) ?>)' 
                                            class="p-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded transition-colors"
                                            title="Editar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/quill.svg" 
                                             alt="Editar" class="w-4 h-4 icon-primary">
                                    </button>
                                    <button onclick="confirmDeleteElemento(<?= $elemento['id'] ?>, '<?= htmlspecialchars($elemento['nome']) ?>')" 
                                            class="p-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded transition-colors"
                                            title="Deletar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/skull-crossed-bones.svg" 
                                             alt="Deletar" class="w-4 h-4">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($elementos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="text-text/50">
                                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/fire-bowl.svg" 
                                         alt="Sem elementos" 
                                         class="w-16 h-16 mx-auto mb-4 opacity-30 icon-muted">
                                    <p class="text-lg font-heading">Nenhum elemento encontrado</p>
                                    <p class="text-sm mt-2">
                                        <?= $searchTerm ? 'Tente ajustar os filtros' : 'Clique em "Adicionar Elemento" para começar' ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <?php if ($totalPages > 1): ?>
            <div class="border-t border-border/30 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-text/70">
                        Página <?= $page ?> de <?= $totalPages ?>
                    </div>
                    <div class="flex gap-2">
                        <?php
                        // Construir URL base mantendo filtros
                        $baseUrl = SITE_URL . '/public/index.php?page=admin/content-management-rpg/elementos';
                        if ($searchTerm) $baseUrl .= '&search=' . urlencode($searchTerm);
                        if ($orderBy !== 'id') $baseUrl .= '&order=' . urlencode($orderBy);
                        if ($orderDir !== 'ASC') $baseUrl .= '&dir=' . urlencode(strtolower($orderDir));
                        ?>
                        
                        <!-- Primeira página -->
                        <?php if ($page > 1): ?>
                            <a href="<?= $baseUrl ?>&p=1" 
                               class="px-3 py-2 rounded-lg border border-border hover:bg-muted/20 transition-colors">
                                ««
                            </a>
                            <a href="<?= $baseUrl ?>&p=<?= $page - 1 ?>" 
                               class="px-3 py-2 rounded-lg border border-border hover:bg-muted/20 transition-colors">
                                ‹
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-2 rounded-lg border border-border/30 text-text/30 cursor-not-allowed">««</span>
                            <span class="px-3 py-2 rounded-lg border border-border/30 text-text/30 cursor-not-allowed">‹</span>
                        <?php endif; ?>
                        
                        <!-- Páginas numeradas -->
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="<?= $baseUrl ?>&p=<?= $i ?>" 
                               class="px-3 py-2 rounded-lg border <?= $i === $page ? 'bg-primary text-white border-primary' : 'border-border hover:bg-muted/20' ?> transition-colors">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <!-- Última página -->
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= $baseUrl ?>&p=<?= $page + 1 ?>" 
                               class="px-3 py-2 rounded-lg border border-border hover:bg-muted/20 transition-colors">
                                ›
                            </a>
                            <a href="<?= $baseUrl ?>&p=<?= $totalPages ?>" 
                               class="px-3 py-2 rounded-lg border border-border hover:bg-muted/20 transition-colors">
                                »»
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-2 rounded-lg border border-border/30 text-text/30 cursor-not-allowed">›</span>
                            <span class="px-3 py-2 rounded-lg border border-border/30 text-text/30 cursor-not-allowed">»»</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
    </div>
</main>

<!-- Modal de Elemento -->
<div id="elementoModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4" 
     style="display: none; opacity: 0; transition: opacity 0.3s ease;">
    <div class="card max-w-2xl w-full max-h-[90vh] flex flex-col" onclick="event.stopPropagation()">
        
        <!-- Header do Modal -->
        <div class="p-4 border-b border-primary/10 flex items-center justify-between flex-shrink-0">
            <h2 id="modalTitle" class="font-title text-xl font-bold text-primary flex items-center gap-2">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/fire-bowl.svg" alt="Elemento" class="w-6 h-6 icon-primary">
                <span>Novo Elemento</span>
            </h2>
            <button onclick="closeModal('elementoModal')" type="button" class="text-text/60 hover:text-danger transition-colors">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/cancel.svg" 
                     alt="Fechar" 
                     class="w-6 h-6 icon-danger">
            </button>
        </div>
        
        <!-- Formulário (com scroll) -->
        <form id="elementoForm" class="overflow-y-auto flex-1 p-4 space-y-4 scrollbar-thin scrollbar-thumb-primary/20 scrollbar-track-transparent">
            <input type="hidden" id="formAction" name="action" value="create">
            <input type="hidden" id="elementoId" name="id" value="">
            
            <!-- Grid de 2 colunas -->
            <div class="grid md:grid-cols-2 gap-4">
                
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">
                        Nome do Elemento *
                    </label>
                    <input type="text" 
                           id="elementoNome" 
                           name="nome" 
                           required 
                           placeholder="Ex: Fogo, Lava, Ácido..."
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                    <p class="text-xs text-text/60 mt-1">Digite o nome do elemento</p>
                </div>
                
                <!-- Bônus de Dano -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">
                        Bônus de Dano (%)
                    </label>
                    <input type="number" 
                           id="elementoBonus" 
                           name="bonus_dano_percentual"
                           value="50"
                           min="0"
                           max="200"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                </div>
                
            </div>
            
            <!-- Cor -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">
                    Cor (HEX)
                </label>
                <div class="flex gap-2">
                    <input type="color" 
                           id="elementoCor" 
                           name="cor_hex_picker"
                           value="#666666"
                           class="w-16 h-10 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 cursor-pointer">
                    <input type="text" 
                           id="elementoCorHex" 
                           name="cor_hex"
                           value="#666666"
                           placeholder="#FF5733"
                           maxlength="7"
                           class="flex-1 px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                </div>
            </div>
            
            <!-- Grid de 2 colunas -->
            <div class="grid md:grid-cols-2 gap-4">
                
                <!-- Forte Contra -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">
                        Forte Contra
                    </label>
                    <input type="text" 
                           id="elementoForte" 
                           name="forte_contra"
                           placeholder="Ex: agua,gelo"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                    <p class="text-xs text-text/60 mt-1">Separe por vírgula</p>
                </div>
                
                <!-- Fraco Contra -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">
                        Fraco Contra
                    </label>
                    <input type="text" 
                           id="elementoFraco" 
                           name="fraco_contra"
                           placeholder="Ex: fogo,raio"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                    <p class="text-xs text-text/60 mt-1">Separe por vírgula</p>
                </div>
                
            </div>
            
            <!-- Ícone -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">
                    Ícone (opcional)
                </label>
                <input type="text" 
                       id="elementoIcone" 
                       name="icone"
                       placeholder="fire-bowl.svg"
                       class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                <p class="text-xs text-text/60 mt-1">Nome do arquivo SVG</p>
            </div>
            
            <!-- Descrição -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">
                    Descrição
                </label>
                <textarea id="elementoDescricao" 
                          name="descricao" 
                          rows="3"
                          class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text resize-none focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                          placeholder="Descrição do elemento..."></textarea>
            </div>
            
        </form>
        
        <!-- Footer do Modal (Botões) -->
        <div class="p-4 border-t border-primary/10 flex justify-end gap-3 flex-shrink-0">
            <button type="button" onclick="closeModal('elementoModal')" class="btn-outline">
                Cancelar
            </button>
            <button type="button" onclick="saveElemento()" class="btn-primary">
                Salvar Elemento
            </button>
        </div>
        
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
            <p class="text-text/70 mb-1">Tem certeza que deseja excluir o elemento</p>
            <p class="font-semibold text-primary mb-4 capitalize" id="deleteElementoName"></p>
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

<!-- Toast System -->
<script src="<?= SITE_URL ?>/public/js/toast.js"></script>

<script>
let deleteElementoId = null;

// Sistema de Modal
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
    document.body.style.overflow = '';
}

// Fechar modal ao clicar fora
document.getElementById('elementoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('elementoModal');
    }
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Prevenir submit do formulário
document.getElementById('elementoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    return false;
});

// Sincronizar color picker com input hex
document.getElementById('elementoCor').addEventListener('input', function(e) {
    document.getElementById('elementoCorHex').value = e.target.value;
});

document.getElementById('elementoCorHex').addEventListener('input', function(e) {
    let value = e.target.value;
    if (value && value[0] !== '#') {
        value = '#' + value;
    }
    if (/^#[0-9A-F]{6}$/i.test(value)) {
        document.getElementById('elementoCor').value = value;
        document.getElementById('elementoCorHex').value = value;
    }
});

// Abrir modal de criação
function openCreateModal() {
    console.log('Abrindo modal de criação');
    
    document.getElementById('modalTitle').innerHTML = '<img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/fire-bowl.svg" alt="Elemento" class="w-6 h-6 icon-primary"><span>Novo Elemento</span>';
    document.getElementById('formAction').value = 'create';
    document.getElementById('elementoForm').reset();
    document.getElementById('elementoId').value = '';
    document.getElementById('elementoBonus').value = '50';
    document.getElementById('elementoCor').value = '#666666';
    document.getElementById('elementoCorHex').value = '#666666';
    
    showModal('elementoModal');
}

// Editar elemento
function editElemento(elemento) {
    console.log('Editando elemento:', elemento);
    
    document.getElementById('modalTitle').innerHTML = '<img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/fire-bowl.svg" alt="Elemento" class="w-6 h-6 icon-primary"><span>Editar Elemento</span>';
    document.getElementById('formAction').value = 'update';
    document.getElementById('elementoId').value = elemento.id;
    document.getElementById('elementoNome').value = elemento.nome;
    document.getElementById('elementoCor').value = elemento.cor_hex || '#666666';
    document.getElementById('elementoCorHex').value = elemento.cor_hex || '#666666';
    document.getElementById('elementoBonus').value = elemento.bonus_dano_percentual;
    document.getElementById('elementoForte').value = elemento.forte_contra || '';
    document.getElementById('elementoFraco').value = elemento.fraco_contra || '';
    document.getElementById('elementoIcone').value = elemento.icone || '';
    document.getElementById('elementoDescricao').value = elemento.descricao || '';
    
    showModal('elementoModal');
}

// Salvar elemento
async function saveElemento() {
    console.log('Salvando elemento...');
    
    const form = document.getElementById('elementoForm');
    const formData = new FormData(form);
    
    // Validar nome
    if (!formData.get('nome')) {
        toast.error('Por favor, selecione o nome do elemento');
        return;
    }
    
    // Log dos dados
    console.log('Dados do formulário:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    try {
        const response = await fetch('<?= SITE_URL ?>/app/views/admin/content-management-rpg/handlers/elementos_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        console.log('Resposta do servidor (raw):', text);
        
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            console.error('Erro ao fazer parse do JSON:', parseError);
            console.error('Texto recebido:', text);
            toast.error('Erro no servidor. Verifique o console para detalhes.');
            return;
        }
        
        console.log('Resposta do servidor (parsed):', result);
        
        if (result.success) {
            toast.success(result.message);
            closeModal('elementoModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(result.message);
        }
    } catch (error) {
        console.error('Erro completo:', error);
        toast.error('Erro ao salvar elemento: ' + error.message);
    }
}

// Confirmar exclusão
function confirmDeleteElemento(id, nome) {
    console.log('Confirmando exclusão:', id, nome);
    
    deleteElementoId = id;
    document.getElementById('deleteElementoName').textContent = nome;
    
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 10);
}

// Fechar modal de exclusão
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        deleteElementoId = null;
    }, 300);
}

// Executar exclusão
async function executeDelete() {
    if (!deleteElementoId) return;
    
    console.log('Deletando elemento ID:', deleteElementoId);
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', deleteElementoId);
        
        const response = await fetch('<?= SITE_URL ?>/app/views/admin/content-management-rpg/handlers/elementos_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text();
        console.log('Resposta do servidor (raw):', text);
        
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            console.error('Erro ao fazer parse do JSON:', parseError);
            toast.error('Erro no servidor. Verifique o console.');
            return;
        }
        
        console.log('Resposta do servidor (parsed):', result);
        
        if (result.success) {
            toast.success(result.message);
            closeDeleteModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(result.message);
        }
    } catch (error) {
        console.error('Erro completo:', error);
        toast.error('Erro ao deletar elemento: ' + error.message);
    }
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
