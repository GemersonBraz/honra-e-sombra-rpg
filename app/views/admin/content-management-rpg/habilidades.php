<?php
/**
 * Gerenciamento de Habilidades
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
$filterCategoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$filterNivel = isset($_GET['nivel']) ? $_GET['nivel'] : '';
$filterAtivo = isset($_GET['ativo']) ? $_GET['ativo'] : '';
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'id';
$orderDir = isset($_GET['dir']) && $_GET['dir'] === 'desc' ? 'DESC' : 'ASC';

// Construir query com filtros
$where = [];
$params = [];

if ($searchTerm) {
    $where[] = "(nome LIKE :search OR descricao LIKE :search OR tipo LIKE :search)";
    $params[':search'] = "%{$searchTerm}%";
}

if ($filterCategoria) {
    $where[] = "categoria = :categoria";
    $params[':categoria'] = $filterCategoria;
}

if ($filterNivel) {
    $where[] = "nivel_minimo = :nivel";
    $params[':nivel'] = $filterNivel;
}

if ($filterAtivo !== '') {
    $where[] = "ativo = :ativo";
    $params[':ativo'] = (int)$filterAtivo;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Validar coluna de ordenação
$allowedColumns = ['id', 'nome', 'categoria', 'nivel_minimo', 'custo_pontos'];
if (!in_array($orderBy, $allowedColumns)) {
    $orderBy = 'id';
}

// Contar total de habilidades
$countSql = "SELECT COUNT(*) FROM habilidades_disponiveis $whereClause";
$countStmt = $conn->prepare($countSql);
$countStmt->execute($params);
$totalHabilidades = $countStmt->fetchColumn();
$totalPages = ceil($totalHabilidades / $perPage);

// Buscar habilidades com paginação
$sql = "SELECT * FROM habilidades_disponiveis $whereClause ORDER BY $orderBy $orderDir LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$habilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Main Content -->
<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">

        <!-- Card de Título -->
        <div class="card p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crystal-wand.svg" alt="Habilidades" 
                         class="w-12 h-12 icon-primary">
                    <div>
                        <h1 class="font-title text-3xl font-bold text-primary mb-1">Gerenciamento de Habilidades</h1>
                        <p class="text-text/70">Gerencie as habilidades disponíveis do jogo</p>
                    </div>
                </div>
                <button onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg'" 
                        class="btn-outline inline-flex items-center gap-2">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/arrow-dunk.svg" alt="Voltar" 
                         class="w-4 h-4 icon-muted rotate-180">
                    Voltar
                </button>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="card p-4 mb-4">
            <form method="GET" action="" class="grid md:grid-cols-4 gap-4"> 
                <input type="hidden" name="page" value="admin/content-management-rpg/habilidades">

                <!-- Busca por nome/descrição -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/magnifying-glass.svg" alt="Buscar" class="w-4 h-4 icon-primary">
                        Buscar
                    </label>
                    <input type="text" 
                           name="search" 
                           value="<?= htmlspecialchars($searchTerm) ?>"
                           placeholder="Nome, descrição ou tipo..."
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text placeholder:text-text/40 focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                </div>

                <!-- Filtro por Categoria -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crystal-wand.svg" alt="Categoria" class="w-4 h-4 icon-primary">
                        Categoria
                    </label>
                    <select name="categoria" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors capitalize">
                        <option value="">Todas</option>
                        <option value="magia" <?= $filterCategoria === 'magia' ? 'selected' : '' ?>>Magia</option>
                        <option value="tecnica_arma" <?= $filterCategoria === 'tecnica_arma' ? 'selected' : '' ?>>Técnica de Arma</option>
                        <option value="fisico" <?= $filterCategoria === 'fisico' ? 'selected' : '' ?>>Físico</option>
                        <option value="mental" <?= $filterCategoria === 'mental' ? 'selected' : '' ?>>Mental</option>
                    </select>
                </div>

                <!-- Filtro por Nível -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/stairs.svg" alt="Nível" class="w-4 h-4 icon-primary">
                        Nível Mínimo
                    </label>
                    <select name="nivel" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors capitalize">
                        <option value="">Todos</option>
                        <option value="principiante" <?= $filterNivel === 'principiante' ? 'selected' : '' ?>>Principiante</option>
                        <option value="experiente" <?= $filterNivel === 'experiente' ? 'selected' : '' ?>>Experiente</option>
                        <option value="veterano" <?= $filterNivel === 'veterano' ? 'selected' : '' ?>>Veterano</option>
                        <option value="mestre" <?= $filterNivel === 'mestre' ? 'selected' : '' ?>>Mestre</option>
                    </select>
                </div>

                <!-- Filtro por Status -->
                <div>
                    <label class="block text-sm font-semibold text-text/90 mb-2 flex items-center gap-2">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/check-mark.svg" alt="Status" class="w-4 h-4 icon-primary">
                        Status
                    </label>
                    <select name="ativo" class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Todos</option>
                        <option value="1" <?= $filterAtivo === '1' ? 'selected' : '' ?>>Ativo</option>
                        <option value="0" <?= $filterAtivo === '0' ? 'selected' : '' ?>>Inativo</option>
                    </select>
                </div>

                <!-- Botões -->
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="btn-primary inline-flex items-center gap-2 flex-1">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/magnifying-glass.svg" alt="Filtrar" class="w-4 h-4 icon-white">
                        Aplicar Filtros
                    </button>
                    <a href="<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/habilidades" 
                       class="btn-outline inline-flex items-center gap-2 flex-1">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/cancel.svg" alt="Limpar" class="w-4 h-4 icon-muted">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Botão Adicionar Habilidade -->
        <div class="mb-4 flex justify-end">
            <button onclick="openCreateModal()" class="btn-primary inline-flex items-center gap-2">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crystal-wand.svg" alt="Adicionar" 
                     class="w-4 h-4 icon-white">
                Adicionar Habilidade
            </button>
        </div>

        <!-- Tabela de Habilidades -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-amber-400/50 dark:border-amber-600/30">
                            <th class="text-center">ID</th>
                            <th class="text-center">Nome</th>
                            <th class="text-center">Categoria</th>
                            <th class="text-center">Nível Mínimo</th>
                            <th class="text-center">Custo</th>
                            <th class="text-center">Bônus</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($habilidades as $habilidade): ?>
                        <tr class="border-b-2 border-amber-400/50 dark:border-amber-600/30">
                            <td class="font-mono text-sm text-center">#<?= $habilidade['id'] ?></td>
                            <td class="text-center">
                                <span class="font-semibold"><?= htmlspecialchars($habilidade['nome']) ?></span>
                                <?php if ($habilidade['tipo']): ?>
                                    <br><span class="text-xs text-text/60"><?= htmlspecialchars($habilidade['tipo']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold capitalize
                                    <?php
                                    switch($habilidade['categoria']) {
                                        case 'magia': echo 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'; break;
                                        case 'tecnica_arma': echo 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'; break;
                                        case 'fisico': echo 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'; break;
                                        case 'mental': echo 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'; break;
                                    }
                                    ?>">
                                    <?= str_replace('_', ' ', htmlspecialchars($habilidade['categoria'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="px-2 py-1 rounded text-xs font-semibold capitalize
                                    <?php
                                    switch($habilidade['nivel_minimo']) {
                                        case 'principiante': echo 'bg-lime-100 text-lime-700 dark:bg-lime-900/30 dark:text-lime-300'; break;
                                        case 'experiente': echo 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'; break;
                                        case 'veterano': echo 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'; break;
                                        case 'mestre': echo 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'; break;
                                    }
                                    ?>">
                                    <?= htmlspecialchars($habilidade['nivel_minimo']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="font-mono text-sm font-semibold text-primary">
                                    <?= $habilidade['custo_pontos'] ?> pts
                                </span>
                            </td>
                            <td class="text-center text-xs">
                                <?php if ($habilidade['bonus_atk'] > 0): ?>
                                    <span class="text-rose-600 dark:text-rose-400">ATK +<?= $habilidade['bonus_atk'] ?></span><br>
                                <?php endif; ?>
                                <?php if ($habilidade['bonus_def'] > 0): ?>
                                    <span class="text-sky-600 dark:text-sky-400">DEF +<?= $habilidade['bonus_def'] ?></span><br>
                                <?php endif; ?>
                                <?php if ($habilidade['bonus_hp'] > 0): ?>
                                    <span class="text-emerald-600 dark:text-emerald-400">HP +<?= $habilidade['bonus_hp'] ?></span>
                                <?php endif; ?>
                                <?php if (!$habilidade['bonus_atk'] && !$habilidade['bonus_def'] && !$habilidade['bonus_hp']): ?>
                                    <span class="text-text/30">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($habilidade['ativo']): ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-success/20 text-success">
                                        Ativo
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-danger/20 text-danger">
                                        Inativo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='editHabilidade(<?= json_encode($habilidade) ?>)' 
                                            class="p-2 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-900/50 rounded transition-colors"
                                            title="Editar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/quill.svg" 
                                             alt="Editar" class="w-4 h-4 icon-primary">
                                    </button>
                                    <button onclick="confirmDelete(<?= $habilidade['id'] ?>, '<?= htmlspecialchars($habilidade['nome']) ?>')" 
                                            class="p-2 bg-red-100 dark:bg-red-900/30 hover:bg-red-200 dark:hover:bg-red-900/50 rounded transition-colors"
                                            title="Deletar">
                                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/skull-crossed-bones.svg" 
                                             alt="Deletar" class="w-4 h-4">
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($habilidades)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="text-text/50">
                                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crystal-wand.svg" 
                                         alt="Sem habilidades" 
                                         class="w-16 h-16 mx-auto mb-4 opacity-30 icon-muted">
                                    <p class="text-lg font-heading">Nenhuma habilidade encontrada</p>
                                    <p class="text-sm mt-2">
                                        <?= $searchTerm || $filterCategoria || $filterNivel || $filterAtivo !== '' ? 'Tente ajustar os filtros' : 'Clique em "Adicionar Habilidade" para começar' ?>
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
                        $baseUrl = SITE_URL . '/public/index.php?page=admin/content-management-rpg/habilidades';
                        if ($searchTerm) $baseUrl .= '&search=' . urlencode($searchTerm);
                        if ($filterCategoria) $baseUrl .= '&categoria=' . urlencode($filterCategoria);
                        if ($filterNivel) $baseUrl .= '&nivel=' . urlencode($filterNivel);
                        if ($filterAtivo !== '') $baseUrl .= '&ativo=' . urlencode($filterAtivo);
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

<!-- Modal de Edição/Criação -->
<div id="habilidadeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4" 
     style="display: none; opacity: 0; transition: opacity 0.3s ease;">
    <div class="bg-background rounded-xl shadow-2xl max-w-4xl w-full max-h-[80vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
        
        <!-- Header do Modal -->
        <div class="p-4 border-b border-primary/10 flex items-center justify-between bg-background flex-shrink-0">
            <h2 id="modalTitle" class="font-title text-xl font-bold text-primary flex items-center gap-2">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/crystal-wand.svg" alt="Habilidade" class="w-6 h-6 icon-primary">
                <span>Nova Habilidade</span>
            </h2>
            <button onclick="closeModal()" type="button" class="text-text/60 hover:text-danger transition-colors">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/cancel.svg" 
                     alt="Fechar" 
                     class="w-6 h-6 icon-danger">
            </button>
        </div>
        
        <!-- Formulário (com scroll) -->
        <form id="habilidadeForm" class="overflow-y-auto flex-1 p-4 space-y-4 scrollbar-thin scrollbar-thumb-primary/20 scrollbar-track-transparent" enctype="multipart/form-data">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="habilidadeId">
            <input type="hidden" name="removeImagem" id="removeImagem" value="0">
            <input type="hidden" name="imagem" id="habilidadeImagem">
            
            <!-- Linha 1: Nome e Tipo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Nome da Habilidade *</label>
                    <input type="text" name="nome" id="habilidadeNome" required
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                           placeholder="Ex: Bola de Fogo">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Tipo</label>
                    <input type="text" name="tipo" id="habilidadeTipo"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                           placeholder="Ex: Ataque, Buff, Cura">
                </div>
            </div>

            <!-- Linha 2: Categoria e Nível -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Categoria *</label>
                    <select name="categoria" id="habilidadeCategoria" required
                            class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Selecione...</option>
                        <option value="magia">Magia</option>
                        <option value="tecnica_arma">Técnica de Arma</option>
                        <option value="fisico">Físico</option>
                        <option value="mental">Mental</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Nível Mínimo *</label>
                    <select name="nivel_minimo" id="habilidadeNivelMinimo" required
                            class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Selecione...</option>
                        <option value="principiante">Principiante</option>
                        <option value="experiente">Experiente</option>
                        <option value="veterano">Veterano</option>
                        <option value="mestre">Mestre</option>
                    </select>
                </div>
            </div>

            <!-- Linha 3: Classes e Elemento -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Classes Permitidas</label>
                    <input type="text" name="classes_permitidas" id="habilidadeClassesPermitidas"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors"
                           placeholder="Ex: guerreiro,cavaleiro ou deixe vazio para todas">
                    <p class="text-xs text-text/60 mt-1">Separe por vírgula ou deixe vazio para todas as classes</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Elemento Requerido</label>
                    <select name="elemento_requerido" id="habilidadeElementoRequerido"
                            class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Nenhum</option>
                        <option value="fogo">Fogo</option>
                        <option value="agua">Água</option>
                        <option value="terra">Terra</option>
                        <option value="ar">Ar</option>
                        <option value="luz">Luz</option>
                        <option value="trevas">Trevas</option>
                        <option value="raio">Raio</option>
                        <option value="gelo">Gelo</option>
                        <option value="natureza">Natureza</option>
                    </select>
                </div>
            </div>

            <!-- Linha 4: Bônus de Atributos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Bônus ATK</label>
                    <input type="number" name="bonus_atk" id="habilidadeBonusAtk" value="0"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                    <p class="text-xs text-text/60 mt-1">Pode ser positivo ou negativo</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Bônus DEF</label>
                    <input type="number" name="bonus_def" id="habilidadeBonusDef" value="0"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                    <p class="text-xs text-text/60 mt-1">Pode ser positivo ou negativo</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Bônus HP</label>
                    <input type="number" name="bonus_hp" id="habilidadeBonusHp" value="0"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                    <p class="text-xs text-text/60 mt-1">Pode ser positivo ou negativo</p>
                </div>
            </div>

            <!-- Linha 5: Custo e Pré-requisito -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Custo em Pontos</label>
                    <input type="number" name="custo_pontos" id="habilidadeCustoPontos" min="1" value="1"
                           class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-primary mb-2">Pré-requisito (Habilidade)</label>
                    <select name="prerequisito_habilidade_id" id="habilidadePrerequisito"
                            class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors">
                        <option value="">Nenhum</option>
                        <!-- Será preenchido via JavaScript -->
                    </select>
                </div>
            </div>

            <!-- Linha 6: Descrição -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">Descrição</label>
                <textarea name="descricao" id="habilidadeDescricao" rows="3"
                          class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors resize-none"
                          placeholder="Descreva a habilidade..."></textarea>
            </div>

            <!-- Linha 7: Efeito Especial -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">Efeito Especial</label>
                <textarea name="efeito_especial" id="habilidadeEfeitoEspecial" rows="2"
                          class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors resize-none"
                          placeholder="Ex: Queima o alvo por 3 turnos, causando 5 de dano por turno"></textarea>
            </div>

            <!-- Linha 8: Imagem -->
            <div>
                <label class="block text-sm font-semibold text-primary mb-2">Imagem da Habilidade</label>
                <input type="file" name="imagemFile" id="habilidadeImagemFile" accept="image/*"
                       class="w-full px-4 py-2 rounded-lg border-2 border-amber-200 dark:border-amber-600/30 bg-background text-text focus:outline-none focus:border-primary dark:focus:border-primary transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 file:cursor-pointer">
                <p class="text-xs text-text/60 mt-1">Tamanho máximo: 2MB | Formatos: JPG, PNG, GIF, SVG</p>
                
                <!-- Preview da Imagem -->
                <div id="imagePreview" class="mt-3 hidden">
                    <div class="relative inline-block">
                        <img id="previewImg" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-amber-200 dark:border-amber-600/30">
                        <button type="button" onclick="removeImage()" 
                                class="absolute -top-2 -right-2 bg-danger text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-danger/80 transition-colors shadow-lg">
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            <!-- Linha 9: Ativo -->
            <div class="flex items-center gap-3 p-3 rounded-lg bg-primary/5 border border-primary/10">
                <input type="checkbox" name="ativo" id="habilidadeAtivo" value="1" checked
                       class="w-5 h-5 text-primary bg-background border-primary/30 rounded focus:ring-primary focus:ring-2">
                <label for="habilidadeAtivo" class="text-sm font-semibold text-primary cursor-pointer">Habilidade Ativa</label>
            </div>
        </form>

        <!-- Footer com Botões -->
        <div class="p-4 border-t border-primary/10 flex items-center gap-3 bg-background flex-shrink-0">
            <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 rounded-lg border-2 border-border/50 text-text hover:bg-primary/5 hover:border-primary/30 transition-colors font-semibold">
                Cancelar
            </button>
            <button type="submit" form="habilidadeForm" class="flex-1 px-4 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white font-semibold transition-colors shadow-lg shadow-primary/20">
                Salvar Habilidade
            </button>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4" 
     style="display: none; opacity: 0; transition: opacity 0.3s ease;">
    <div class="card max-w-md w-full" onclick="event.stopPropagation()">
        
        <!-- Conteúdo -->
        <div class="p-6 text-center">
            <div class="w-16 h-16 mx-auto bg-danger/10 rounded-full flex items-center justify-center mb-4">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/skull-crossed-bones.svg" alt="Deletar" 
                     class="w-8 h-8 icon-danger">
            </div>
            
            <h3 class="font-title text-xl font-bold text-primary mb-2">Confirmar Exclusão</h3>
            <p class="text-text/70 mb-1">Tem certeza que deseja excluir a Habilidade</p>
            <p class="font-semibold text-primary mb-4" id="deleteHabilidadeName"></p>
            <p class="text-sm text-danger font-semibold flex items-center justify-center gap-2">
                <span>⚠️</span>
                <span>Esta ação não pode ser desfeita!</span>
            </p>
        </div>
        
        <!-- Botões -->
        <div class="p-4 border-t border-primary/10 flex items-center gap-3 bg-background/50">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 rounded-lg border-2 border-border/50 text-text hover:bg-primary/5 hover:border-primary/30 transition-colors font-semibold">
                Cancelar
            </button>
            <button onclick="executeDelete()" class="flex-1 px-4 py-2 rounded-lg bg-danger hover:bg-danger/80 text-white font-semibold transition-colors shadow-lg shadow-danger/20">
                Deletar
            </button>
        </div>
    </div>
</div>

<script>
let deleteHabilidadeId = null;

// Carregar habilidades para o dropdown de pré-requisitos
async function loadHabilidadesPrerequisitos() {
    try {
        const response = await fetch('/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/habilidades_handler.php?action=list');
        const result = await response.json();
        
        const select = document.getElementById('habilidadePrerequisito');
        // Limpar opções exceto "Nenhum"
        select.innerHTML = '<option value="">Nenhum</option>';
        
        if (result.success && result.habilidades) {
            result.habilidades.forEach(hab => {
                const option = document.createElement('option');
                option.value = hab.id;
                option.textContent = hab.nome;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erro ao carregar habilidades:', error);
    }
}

// Preview de imagem
document.getElementById('habilidadeImagemFile').addEventListener('change', function(e) {
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
    document.getElementById('habilidadeImagemFile').value = '';
    document.getElementById('habilidadeImagem').value = '';
    document.getElementById('removeImagem').value = '1';
    document.getElementById('imagePreview').classList.add('hidden');
}

// Abrir modal de criação
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Nova Habilidade';
    document.getElementById('formAction').value = 'create';
    document.getElementById('habilidadeForm').reset();
    document.getElementById('habilidadeId').value = '';
    document.getElementById('habilidadeAtivo').checked = true;
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('removeImagem').value = '0';
    
    // Resetar valores padrão
    document.getElementById('habilidadeBonusAtk').value = '0';
    document.getElementById('habilidadeBonusDef').value = '0';
    document.getElementById('habilidadeBonusHp').value = '0';
    document.getElementById('habilidadeCustoPontos').value = '1';
    
    loadHabilidadesPrerequisitos();
    showModal('habilidadeModal');
}

// Editar habilidade
function editHabilidade(habilidade) {
    console.log('Editando habilidade:', habilidade);
    
    document.getElementById('modalTitle').textContent = 'Editar Habilidade';
    document.getElementById('formAction').value = 'update';
    document.getElementById('habilidadeId').value = habilidade.id;
    document.getElementById('habilidadeNome').value = habilidade.nome || '';
    document.getElementById('habilidadeTipo').value = habilidade.tipo || '';
    document.getElementById('habilidadeCategoria').value = habilidade.categoria || '';
    document.getElementById('habilidadeNivelMinimo').value = habilidade.nivel_minimo || '';
    document.getElementById('habilidadeClassesPermitidas').value = habilidade.classes_permitidas || '';
    document.getElementById('habilidadeElementoRequerido').value = habilidade.elemento_requerido || '';
    document.getElementById('habilidadeBonusAtk').value = habilidade.bonus_atk || '0';
    document.getElementById('habilidadeBonusDef').value = habilidade.bonus_def || '0';
    document.getElementById('habilidadeBonusHp').value = habilidade.bonus_hp || '0';
    document.getElementById('habilidadeEfeitoEspecial').value = habilidade.efeito_especial || '';
    document.getElementById('habilidadeCustoPontos').value = habilidade.custo_pontos || '1';
    document.getElementById('habilidadeDescricao').value = habilidade.descricao || '';
    document.getElementById('habilidadeImagem').value = habilidade.imagem || '';
    document.getElementById('habilidadeAtivo').checked = habilidade.ativo == 1;
    
    // Carregar pré-requisitos e depois selecionar o correto
    loadHabilidadesPrerequisitos().then(() => {
        if (habilidade.prerequisito_habilidade_id) {
            document.getElementById('habilidadePrerequisito').value = habilidade.prerequisito_habilidade_id;
        }
    });
    
    // Mostrar preview se houver imagem
    if (habilidade.imagem) {
        document.getElementById('previewImg').src = '<?= SITE_URL ?>/' + habilidade.imagem;
        document.getElementById('imagePreview').classList.remove('hidden');
    } else {
        document.getElementById('imagePreview').classList.add('hidden');
    }

    showModal('habilidadeModal');
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
    const modal = document.getElementById('habilidadeModal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Confirmar exclusão
function confirmDelete(id, nome) {
    deleteHabilidadeId = id;
    document.getElementById('deleteHabilidadeName').textContent = nome;
    showModal('deleteModal');
}

// Fechar modal de exclusão
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        deleteHabilidadeId = null;
    }, 300);
}

// Executar exclusão
async function executeDelete() {
    if (!deleteHabilidadeId) return;

    console.log('Deletando habilidade ID:', deleteHabilidadeId);

    try {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', deleteHabilidadeId);

        const response = await fetch('/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/habilidades_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        console.log('Resposta do delete:', result);
        
        if (result.success) {
            toast.success(result.message);
            closeDeleteModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(result.message);
        }
    } catch (error) {
        console.error('Erro ao deletar habilidade:', error);
        toast.error('Erro ao deletar habilidade: ' + error.message);
    }
}

// Submit do formulário
document.getElementById('habilidadeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('Enviando formulário de habilidade...');
    
    const formData = new FormData(this);
    
    // Garantir que o checkbox ativo sempre envie 0 ou 1
    const ativoCheckbox = document.getElementById('habilidadeAtivo');
    formData.set('ativo', ativoCheckbox.checked ? '1' : '0');
    
    // Log dos dados
    console.log('Dados do formulário:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    try {
        const response = await fetch('/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/habilidades_handler.php', {
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
            closeModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(result.message);
        }
    } catch (error) {
        console.error('Erro completo:', error);
        toast.error('Erro ao salvar habilidade: ' + error.message);
    }
});

// Fechar modal ao clicar fora
document.getElementById('habilidadeModal').addEventListener('click', closeModal);
document.getElementById('deleteModal').addEventListener('click', closeDeleteModal);

// Carregar habilidades ao abrir o modal pela primeira vez
window.addEventListener('load', () => {
    loadHabilidadesPrerequisitos();
});
</script>


<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>


