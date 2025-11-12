<?php
/**
 * Gerenciamento de Armas
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
$filterTipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$filterRaridade = isset($_GET['raridade']) ? $_GET['raridade'] : '';
$filterClasse = isset($_GET['classe']) ? $_GET['classe'] : '';
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'id';
$orderDir = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

// Construir query com filtros
$where = [];
$params = [];

if ($searchTerm) {
    $where[] = "(nome LIKE :search OR descricao LIKE :search)";
    $params[':search'] = "%{$searchTerm}%";
}

if ($filterTipo) {
    $where[] = "tipo = :tipo";
    $params[':tipo'] = $filterTipo;
}

if ($filterRaridade) {
    $where[] = "raridade = :raridade";
    $params[':raridade'] = $filterRaridade;
}

if ($filterClasse) {
    $where[] = "(classes_permitidas LIKE :classe)";
    $params[':classe'] = '%"' . $filterClasse . '"%';
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Contar total de registros (com filtros)
$countQuery = "SELECT COUNT(*) FROM weapons {$whereClause}";
$stmtCount = $conn->prepare($countQuery);
$stmtCount->execute($params);
$totalWeapons = $stmtCount->fetchColumn();
$totalPages = ceil($totalWeapons / $perPage);

// Validar colunas permitidas para ordenação (segurança)
$allowedOrder = ['id', 'nome', 'tipo', 'atk_bonus', 'def_bonus', 'preco', 'raridade', 'nivel_minimo'];
if (!in_array($orderBy, $allowedOrder)) {
    $orderBy = 'id';
}

// Buscar armas com paginação e filtros
$query = "SELECT * FROM weapons {$whereClause} ORDER BY {$orderBy} {$orderDir} LIMIT {$perPage} OFFSET {$offset}";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$weapons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar classes para dropdown (classes_permitidas)
$classes = $conn->query("SELECT nome FROM classes ORDER BY nome")->fetchAll(PDO::FETCH_COLUMN);

// Buscar elementos para dropdown
$elementos = $conn->query("SELECT nome FROM elementos ORDER BY nome")->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Card de Título -->
        <div class="card border-l-4 border-emerald-500 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-emerald-500/10 flex items-center justify-center">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" alt="Armas" 
                             class="w-8 h-8 icon-primary">
                    </div>
                    <div>
                        <h1 class="font-title text-2xl font-bold text-primary">Gerenciar Armas</h1>
                        <p class="text-text/70 text-sm mt-1">
                            Total de <?= $totalWeapons ?> armas cadastradas • Mostrando <?= count($weapons) ?> de <?= $totalWeapons ?>
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
        
        <!-- Filtros e Busca -->
        <div class="card p-4 mb-4">
            <form method="GET" action="" class="grid md:grid-cols-5 gap-4">
                <input type="hidden" name="page" value="admin/content-management-rpg/weapons">
                
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
                
                <!-- Filtro por Tipo -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" alt="Tipo" class="w-4 h-4 icon-primary">
                        Tipo
                    </label>
                    <select name="tipo" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Todos os tipos</option>
                        <option value="arma_leve" <?= $filterTipo === 'arma_leve' ? 'selected' : '' ?>>Arma Leve</option>
                        <option value="arma_pesada" <?= $filterTipo === 'arma_pesada' ? 'selected' : '' ?>>Arma Pesada</option>
                        <option value="duas_armas" <?= $filterTipo === 'duas_armas' ? 'selected' : '' ?>>Duas Armas</option>
                        <option value="arma_especial" <?= $filterTipo === 'arma_especial' ? 'selected' : '' ?>>Arma Especial</option>
                        <option value="escudo" <?= $filterTipo === 'escudo' ? 'selected' : '' ?>>Escudo</option>
                        <option value="sem_categoria" <?= $filterTipo === 'sem_categoria' ? 'selected' : '' ?>>Sem Categoria</option>
                    </select>
                </div>
                
                <!-- Filtro por Raridade -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/gem-necklace.svg" alt="Raridade" class="w-4 h-4 icon-primary">
                        Raridade
                    </label>
                    <select name="raridade" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Todas raridades</option>
                        <option value="comum" <?= $filterRaridade === 'comum' ? 'selected' : '' ?>>Comum</option>
                        <option value="incomum" <?= $filterRaridade === 'incomum' ? 'selected' : '' ?>>Incomum</option>
                        <option value="raro" <?= $filterRaridade === 'raro' ? 'selected' : '' ?>>Raro</option>
                        <option value="epico" <?= $filterRaridade === 'epico' ? 'selected' : '' ?>>Épico</option>
                        <option value="lendario" <?= $filterRaridade === 'lendario' ? 'selected' : '' ?>>Lendário</option>
                    </select>
                </div>
                
                <!-- Filtro por Classe -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crowned-skull.svg" alt="Classe" class="w-4 h-4 icon-primary">
                        Classe
                    </label>
                    <select name="classe" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Todas as classes</option>
                        <?php foreach ($classes as $classe): ?>
                            <option value="<?= htmlspecialchars($classe) ?>" <?= $filterClasse === $classe ? 'selected' : '' ?>>
                                <?= ucfirst(htmlspecialchars($classe)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                        <option value="tipo" <?= $orderBy === 'tipo' ? 'selected' : '' ?>>Tipo</option>
                        <option value="atk_bonus" <?= $orderBy === 'atk_bonus' ? 'selected' : '' ?>>ATK</option>
                        <option value="def_bonus" <?= $orderBy === 'def_bonus' ? 'selected' : '' ?>>DEF</option>
                        <option value="preco" <?= $orderBy === 'preco' ? 'selected' : '' ?>>Preço</option>
                        <option value="raridade" <?= $orderBy === 'raridade' ? 'selected' : '' ?>>Raridade</option>
                    </select>
                </div>
                
                <!-- Botões -->
                <div class="md:col-span-5 flex gap-2 justify-end">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/magnifying-glass.svg" alt="Filtrar" class="w-4 h-4 icon-white">
                        Aplicar Filtros
                    </button>
                    <a href="<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/weapons" 
                       class="btn-outline inline-flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/cancel.svg" alt="Limpar" class="w-4 h-4 icon-muted">
                        Limpar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Botão Adicionar Arma -->
        <div class="mb-4 flex justify-end">
            <button onclick="openCreateModal()" class="btn-primary inline-flex items-center gap-2">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" alt="Adicionar" 
                     class="w-4 h-4 icon-white">
                Adicionar Arma
            </button>
        </div>
        
        <!-- Tabela de Armas -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="user-table w-full">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagem</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>ATK</th>
                            <th>DEF</th>
                            <th>Preço</th>
                            <th>Raridade</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($weapons as $weapon): ?>
                        <!-- Primeira linha: Dados principais da arma -->
                        <tr class="border-b border-amber-200/30 dark:border-border/10">
                            <td class="font-mono text-sm">#<?= $weapon['id'] ?></td>
                            <td>
                                <?php if ($weapon['imagem']): ?>
                                    <img src="<?= SITE_URL ?>/<?= htmlspecialchars($weapon['imagem']) ?>" 
                                         alt="<?= htmlspecialchars($weapon['nome']) ?>" 
                                         class="w-12 h-12 rounded-lg object-cover border-2 border-border/30">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-lg bg-muted/30 flex items-center justify-center">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" 
                                             alt="Sem imagem" 
                                             class="w-6 h-6 icon-muted opacity-30">
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="font-semibold capitalize">
                                    <?= htmlspecialchars($weapon['nome']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-muted/40 text-text/80 capitalize">
                                    <?= str_replace('_', ' ', htmlspecialchars($weapon['tipo'])) ?>
                                </span>
                            </td>
                            <td class="font-mono text-sm">
                                <span class="text-rose-600 dark:text-rose-400 font-semibold">
                                    <?= $weapon['atk_bonus'] > 0 ? '+' : '' ?><?= $weapon['atk_bonus'] ?>
                                </span>
                            </td>
                            <td class="font-mono text-sm">
                                <span class="text-blue-600 dark:text-blue-400 font-semibold">
                                    <?= $weapon['def_bonus'] > 0 ? '+' : '' ?><?= $weapon['def_bonus'] ?>
                                </span>
                            </td>
                            <td class="font-mono text-sm">
                                <span class="text-amber-600 dark:text-amber-400 font-semibold">
                                    <?= number_format($weapon['preco'], 2) ?> 💰
                                </span>
                            </td>
                            <td>
                                <?php
                                $raridadeColors = [
                                    'comum' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300',
                                    'incomum' => 'bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400',
                                    'raro' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                    'epico' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                                    'lendario' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'
                                ];
                                $colorClass = $raridadeColors[$weapon['raridade']] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300';
                                ?>
                                <span class="inline-block px-2 py-1 text-xs rounded-full <?= $colorClass ?> font-semibold capitalize">
                                    <?= htmlspecialchars($weapon['raridade']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="inline-block px-2 py-1 text-xs rounded-full <?= $weapon['ativo'] ? 'bg-success/20 text-success' : 'bg-danger/20 text-danger' ?>">
                                    <?= $weapon['ativo'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='editWeapon(<?= json_encode($weapon) ?>)' 
                                            class="p-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded transition-colors"
                                            title="Editar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/quill.svg" 
                                             alt="Editar" class="w-4 h-4 icon-primary">
                                    </button>
                                    <button onclick="confirmDeleteWeapon(<?= $weapon['id'] ?>, '<?= htmlspecialchars($weapon['nome']) ?>')" 
                                            class="p-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded transition-colors"
                                            title="Deletar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/skull-crossed-bones.svg" 
                                             alt="Deletar" class="w-4 h-4">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- Segunda linha: Classes permitidas -->
                        <tr class="bg-muted/5 border-b-2 border-amber-400/50 dark:border-border/30">
                            <td colspan="10" class="px-4 py-2">
                                <div class="flex items-start gap-2">
                                    <span class="text-xs font-semibold text-primary whitespace-nowrap">Classes:</span>
                                    <div class="flex flex-wrap gap-1">
                                        <?php
                                        $classesPermitidas = $weapon['classes_permitidas'] ? json_decode($weapon['classes_permitidas'], true) : [];
                                        if (!empty($classesPermitidas)):
                                            foreach ($classesPermitidas as $classe):
                                        ?>
                                            <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-primary/10 text-primary capitalize border border-primary/20">
                                                <?= htmlspecialchars($classe) ?>
                                            </span>
                                        <?php 
                                            endforeach;
                                        else:
                                        ?>
                                            <span class="text-xs text-text/50 italic">Nenhuma classe permitida</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($weapons)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-12">
                                <div class="text-text/50">
                                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" 
                                         alt="Sem armas" 
                                         class="w-16 h-16 mx-auto mb-4 opacity-30 icon-muted">
                                    <p class="text-lg font-heading">Nenhuma arma encontrada</p>
                                    <p class="text-sm mt-2">
                                        <?= $searchTerm || $filterTipo || $filterRaridade || $filterClasse ? 'Tente ajustar os filtros' : 'Clique em "Adicionar Arma" para começar' ?>
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
                        $baseUrl = SITE_URL . '/public/index.php?page=admin/content-management-rpg/weapons';
                        if ($searchTerm) $baseUrl .= '&search=' . urlencode($searchTerm);
                        if ($filterTipo) $baseUrl .= '&tipo=' . urlencode($filterTipo);
                        if ($filterRaridade) $baseUrl .= '&raridade=' . urlencode($filterRaridade);
                        if ($filterClasse) $baseUrl .= '&classe=' . urlencode($filterClasse);
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

<!-- Modal de Criação/Edição de Arma -->
<div id="weaponModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4" 
     style="display: none; opacity: 0; transition: opacity 0.3s ease;">
    <div class="card max-w-4xl w-full h-[600px] flex flex-col" onclick="event.stopPropagation()">
        
        <!-- Header do Modal -->
        <div class="p-4 border-b border-primary/10 flex-shrink-0">
            <div class="flex items-center justify-between">
                <h2 id="modalTitle" class="font-title text-xl font-bold text-primary flex items-center gap-3">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" 
                         alt="Arma" 
                         class="w-6 h-6 icon-primary">
                    <span>Nova Arma</span>
                </h2>
                <button onclick="closeModal('weaponModal')" type="button" class="text-text/60 hover:text-danger transition-colors">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/cancel.svg" 
                         alt="Fechar" 
                         class="w-6 h-6 icon-danger">
                </button>
            </div>
        </div>
        
        <!-- Formulário (com scroll) -->
        <form id="weaponForm" enctype="multipart/form-data" class="overflow-y-auto flex-1 scrollbar-thin scrollbar-thumb-primary/20 scrollbar-track-transparent">
            <div class="p-4 space-y-4">
                <input type="hidden" id="formAction" name="action" value="create">
                <input type="hidden" id="weaponId" name="id" value="">
                <input type="hidden" id="weaponImagem" name="imagem" value="">
                <input type="hidden" id="removeImagem" name="remove_imagem" value="0">
                
                <!-- Grid de 2 colunas -->
                <div class="grid md:grid-cols-2 gap-4">
                    
                    <!-- Coluna 1 -->
                    <div class="space-y-3">
                        
                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Nome da Arma *
                            </label>
                            <input type="text" 
                                   id="weaponNome" 
                                   name="nome" 
                                   required 
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                                   placeholder="Ex: Katana Reluzente">
                        </div>
                        
                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Tipo de Arma *
                            </label>
                            <select id="weaponTipo" name="tipo" required class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                                <option value="">Selecione...</option>
                                <option value="arma_leve">Arma Leve</option>
                                <option value="arma_pesada">Arma Pesada</option>
                                <option value="duas_armas">Duas Armas</option>
                                <option value="arma_especial">Arma Especial</option>
                                <option value="escudo">Escudo</option>
                                <option value="sem_categoria">Sem Categoria</option>
                            </select>
                        </div>
                        
                        <!-- ATK Bonus -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Bônus de Ataque (ATK)
                            </label>
                            <input type="number" 
                                   id="weaponAtkBonus" 
                                   name="atk_bonus" 
                                   value="0"
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                                   placeholder="0">
                        </div>
                        
                        <!-- DEF Bonus -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Bônus de Defesa (DEF)
                            </label>
                            <input type="number" 
                                   id="weaponDefBonus" 
                                   name="def_bonus" 
                                   value="0"
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                                   placeholder="0">
                        </div>
                        
                        <!-- Preço -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Preço (moedas) *
                            </label>
                            <input type="number" 
                                   id="weaponPreco" 
                                   name="preco" 
                                   step="0.01"
                                   required
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                                   placeholder="100.00">
                        </div>
                        
                        <!-- Raridade -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Raridade *
                            </label>
                            <select id="weaponRaridade" name="raridade" required class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                                <option value="comum">Comum</option>
                                <option value="incomum">Incomum</option>
                                <option value="raro">Raro</option>
                                <option value="epico">Épico</option>
                                <option value="lendario">Lendário</option>
                            </select>
                        </div>
                        
                    </div>
                    
                    <!-- Coluna 2 -->
                    <div class="space-y-3">
                        
                        <!-- Nível Mínimo -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Nível Mínimo
                            </label>
                            <input type="number" 
                                   id="weaponNivelMinimo" 
                                   name="nivel_minimo" 
                                   value="1"
                                   min="1"
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                                   placeholder="1">
                        </div>
                        
                        <!-- Durabilidade -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Durabilidade Máxima
                            </label>
                            <input type="number" 
                                   id="weaponDurabilidade" 
                                   name="durabilidade_max" 
                                   value="100"
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                                   placeholder="100">
                        </div>
                        
                        <!-- Peso -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Peso
                            </label>
                            <input type="number" 
                                   id="weaponPeso" 
                                   name="peso" 
                                   value="1"
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                                   placeholder="1">
                        </div>
                        
                        <!-- Elemento Afinidade -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Elemento de Afinidade
                            </label>
                            <select id="weaponElemento" name="elemento_afinidade" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                                <option value="">Nenhum</option>
                                <?php foreach ($elementos as $elemento): ?>
                                    <option value="<?= htmlspecialchars($elemento) ?>"><?= htmlspecialchars(ucfirst($elemento)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Classes Permitidas (Multi-select) -->
                        <div>
                            <label class="block text-sm font-semibold text-primary mb-2">
                                Classes Permitidas
                            </label>
                            <div class="border-2 border-amber-200 dark:border-amber-600/30 rounded-lg p-3 max-h-32 overflow-y-auto scrollbar-thin bg-background">
                                <label class="flex items-center gap-2 mb-2 cursor-pointer hover:bg-muted/10 p-1 rounded">
                                    <input type="checkbox" id="classAll" onchange="toggleAllClasses(this)" class="w-4 h-4">
                                    <span class="text-sm font-semibold text-primary">Todas as Classes</span>
                                </label>
                                <div class="border-t border-border/30 pt-2 mt-2">
                                    <?php foreach ($classes as $classe): ?>
                                        <label class="flex items-center gap-2 mb-1 cursor-pointer hover:bg-muted/10 p-1 rounded">
                                            <input type="checkbox" 
                                                   value="<?= htmlspecialchars($classe) ?>"
                                                   class="weapon-class-checkbox w-4 h-4">
                                            <span class="text-sm capitalize text-text"><?= htmlspecialchars($classe) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Ativo -->
                        <div>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" 
                                       id="weaponAtivo" 
                                       name="ativo" 
                                       value="1"
                                       checked 
                                       class="w-5 h-5 text-primary">
                                <span class="text-sm font-semibold text-primary">Arma Ativa (disponível na loja)</span>
                            </label>
                        </div>
                        
                    </div>
                    
                </div>
                
                <!-- Descrição (full width) -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">
                        Descrição
                    </label>
                    <textarea id="weaponDescricao" 
                              name="descricao" 
                              rows="3"
                              class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text resize-none focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                              placeholder="Descrição detalhada da arma, história, características especiais..."></textarea>
                </div>
                
                <!-- Efeito Especial (full width) -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">
                        Efeito Especial
                    </label>
                    <textarea id="weaponEfeito" 
                              name="efeito_especial" 
                              rows="2"
                              class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text resize-none focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                              placeholder="Ex: Causa dano de fogo adicional, aumenta velocidade..."></textarea>
                </div>
                
                <!-- Upload de Imagem -->
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">
                        Imagem da Arma
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <input type="file" 
                                   id="weaponImagemFile" 
                                   name="imagem_file"
                                   accept="image/*"
                                   onchange="previewWeaponImage(this)"
                                   class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                            <p class="text-xs text-text/60 mt-1">
                                Formatos: JPG, PNG, GIF, WEBP • Tamanho máximo: 2MB
                            </p>
                        </div>
                        <div id="imagePreview" class="hidden">
                            <div class="relative">
                                <img id="previewImg" src="" alt="Preview" class="w-24 h-24 rounded-lg object-cover border-2 border-primary">
                                <button type="button" 
                                        onclick="removeImage()"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-danger text-white rounded-full flex items-center justify-center hover:bg-danger/80 transition-colors"
                                        title="Remover imagem">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </form>
        
        <!-- Footer do Modal (Botões) -->
        <div class="p-4 border-t border-primary/10 flex justify-end gap-3 flex-shrink-0">
            <button type="button" onclick="closeModal('weaponModal')" class="btn-outline">
                Cancelar
            </button>
            <button type="button" onclick="saveWeapon()" class="btn-primary">
                Salvar Arma
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
            <p class="text-text/70 mb-1">Tem certeza que deseja excluir a arma</p>
            <p class="font-semibold text-primary mb-4" id="deleteWeaponName"></p>
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
let deleteWeaponId = null;

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
document.getElementById('weaponModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal('weaponModal');
    }
});

// Prevenir submit do formulário (usamos saveWeapon() ao invés)
document.getElementById('weaponForm').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Submit do formulário bloqueado - use o botão Salvar');
    return false;
});

// Abrir modal de criação
function openCreateModal() {
    document.getElementById('modalTitle').innerHTML = '<img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" alt="Arma" class="w-6 h-6 icon-primary"><span>Nova Arma</span>';
    document.getElementById('formAction').value = 'create';
    document.getElementById('weaponForm').reset();
    document.getElementById('weaponId').value = '';
    document.getElementById('weaponAtivo').checked = true;
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('removeImagem').value = '0';
    document.getElementById('classAll').checked = false;
    
    showModal('weaponModal');
}

// Editar arma
function editWeapon(weapon) {
    document.getElementById('modalTitle').innerHTML = '<img src="<?= SITE_URL ?>/public/img/icons-1x1/delapouite/diamond-hilt.svg" alt="Arma" class="w-6 h-6 icon-primary"><span>Editar Arma</span>';
    document.getElementById('formAction').value = 'update';
    document.getElementById('weaponId').value = weapon.id;
    document.getElementById('weaponNome').value = weapon.nome;
    document.getElementById('weaponTipo').value = weapon.tipo;
    document.getElementById('weaponAtkBonus').value = weapon.atk_bonus;
    document.getElementById('weaponDefBonus').value = weapon.def_bonus;
    document.getElementById('weaponPreco').value = weapon.preco;
    document.getElementById('weaponRaridade').value = weapon.raridade;
    document.getElementById('weaponNivelMinimo').value = weapon.nivel_minimo;
    document.getElementById('weaponDurabilidade').value = weapon.durabilidade_max;
    document.getElementById('weaponPeso').value = weapon.peso;
    document.getElementById('weaponElemento').value = weapon.elemento_afinidade || '';
    document.getElementById('weaponDescricao').value = weapon.descricao || '';
    document.getElementById('weaponEfeito').value = weapon.efeito_especial || '';
    document.getElementById('weaponAtivo').checked = weapon.ativo == 1;
    document.getElementById('weaponImagem').value = weapon.imagem || '';
    document.getElementById('removeImagem').value = '0';
    
    // Classes permitidas (JSON parse)
    const classesPermitidas = weapon.classes_permitidas ? JSON.parse(weapon.classes_permitidas) : [];
    document.querySelectorAll('.weapon-class-checkbox').forEach(checkbox => {
        checkbox.checked = classesPermitidas.includes(checkbox.value);
    });
    
    // Preview da imagem existente
    if (weapon.imagem) {
        document.getElementById('previewImg').src = '<?= SITE_URL ?>/' + weapon.imagem;
        document.getElementById('imagePreview').classList.remove('hidden');
    } else {
        document.getElementById('imagePreview').classList.add('hidden');
    }
    
    showModal('weaponModal');
}

// Preview de imagem
function previewWeaponImage(input) {
    if (input.files && input.files[0]) {
        // Validar tamanho (2MB)
        if (input.files[0].size > 2 * 1024 * 1024) {
            showToast('Imagem muito grande! Máximo 2MB', 'error');
            input.value = '';
            return;
        }
        
        // Validar tipo
        if (!input.files[0].type.startsWith('image/')) {
            showToast('Arquivo deve ser uma imagem', 'error');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
            document.getElementById('removeImagem').value = '0';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remover imagem
function removeImage() {
    document.getElementById('weaponImagemFile').value = '';
    document.getElementById('weaponImagem').value = '';
    document.getElementById('removeImagem').value = '1';
    document.getElementById('imagePreview').classList.add('hidden');
}

// Toggle todas as classes
function toggleAllClasses(checkbox) {
    document.querySelectorAll('.weapon-class-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

// Salvar arma
async function saveWeapon() {
    console.log('=== INICIANDO SALVAMENTO ===');
    
    const form = document.getElementById('weaponForm');
    if (!form) {
        console.error('Formulário não encontrado!');
        toast.error('Erro: Formulário não encontrado');
        return;
    }
    
    const formData = new FormData(form);
    
    // Debug: mostrar todos os dados do formulário
    console.log('Dados do formulário:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }
    
    // Coletar classes permitidas selecionadas
    const classesPermitidas = Array.from(document.querySelectorAll('.weapon-class-checkbox:checked'))
        .map(cb => cb.value);
    
    console.log('Classes permitidas:', classesPermitidas);
    
    // Adicionar como JSON no FormData
    formData.set('classes_permitidas', JSON.stringify(classesPermitidas));
    
    // Garantir que o checkbox ativo sempre envie 0 ou 1
    const ativoCheckbox = document.getElementById('weaponAtivo');
    const ativoValue = ativoCheckbox.checked ? '1' : '0';
    formData.set('ativo', ativoValue);
    
    console.log('Checkbox ativo:', ativoCheckbox.checked, '-> Valor:', ativoValue);
    
    try {
        console.log('Enviando requisição...');
        const response = await fetch('<?= SITE_URL ?>/app/views/admin/content-management-rpg/handlers/weapons_handler.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Resposta recebida, status:', response.status);
        
        const text = await response.text();
        console.log('Resposta do servidor (texto):', text);
        
        let result;
        try {
            result = JSON.parse(text);
            console.log('JSON parseado:', result);
        } catch (parseError) {
            console.error('Erro ao fazer parse do JSON:', parseError);
            console.error('Texto recebido:', text);
            toast.error('Erro no servidor. Verifique o console para detalhes.');
            return;
        }
        
        if (result.success) {
            console.log('Sucesso! Recarregando página...');
            toast.success(result.message);
            closeModal('weaponModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            console.error('Erro no servidor:', result.message);
            toast.error(result.message);
        }
    } catch (error) {
        console.error('Erro completo:', error);
        toast.error('Erro ao salvar arma: ' + error.message);
    }
}

// Confirmar exclusão (abre modal)
function confirmDeleteWeapon(id, nome) {
    deleteWeaponId = id;
    document.getElementById('deleteWeaponName').textContent = `"${nome}"`;
    
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
        deleteWeaponId = null;
    }, 300);
}

// Executar exclusão
async function executeDelete() {
    if (!deleteWeaponId) return;
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', deleteWeaponId);
        
        const response = await fetch('<?= SITE_URL ?>/app/views/admin/content-management-rpg/handlers/weapons_handler.php', {
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
        toast.error('Erro ao deletar arma: ' + error.message);
    }
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
