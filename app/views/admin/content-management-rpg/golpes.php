<?php
/**
 * Gerenciamento de Golpes Especiais
 */

// Verificar se é admin
if (!isAdmin()) {
    redirect('/');
    exit;
}

// Paginação
$itensPorPagina = 6;
$paginaAtual = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($paginaAtual - 1) * $itensPorPagina;

// URL base para paginação
$baseUrl = SITE_URL . '/public/index.php?page=admin/content-management-rpg/golpes';

// Buscar golpes do banco
$pdo = getDB();

// Contar total de golpes
$stmtCount = $pdo->query("SELECT COUNT(*) FROM golpes_templates");
$totalGolpes = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalGolpes / $itensPorPagina);

// Buscar golpes da página atual
$stmt = $pdo->prepare("SELECT * FROM golpes_templates ORDER BY id ASC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$golpes = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/navbar.php';
?>

<main class="flex-1 bg-background">
    <div class="container mx-auto px-4 py-8">
        
        <!-- Card de Título -->
        <div class="card mb-6">
            <div class="p-6 flex items-center justify-between border-b border-primary/10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-rose-500/20 to-rose-600/10 flex items-center justify-center">
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/sword-clash.svg" alt="Golpes" 
                             class="w-7 h-7 icon-primary">
                    </div>
                    <div>
                        <h1 class="font-title text-2xl font-bold text-primary">Golpes Especiais</h1>
                        <p class="text-text/60 text-sm">Gerenciamento de golpes e técnicas de combate</p>
                    </div>
                </div>
                <button onclick="window.location.href='<?= SITE_URL ?>/public/index.php?page=admin/content-management-rpg/index'" 
                        class="px-4 py-2 rounded-lg border-2 border-border/50 text-text hover:bg-primary/5 hover:border-primary/30 transition-colors font-semibold inline-flex items-center gap-2">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/arrow-dunk.svg" alt="Voltar" 
                         class="w-4 h-4 icon-muted rotate-180">
                    Voltar
                </button>
            </div>
        </div>

        <!-- Botão Adicionar -->
        <div class="mb-6">
            <button type="button" onclick="openModal()" class="px-6 py-3 rounded-lg bg-primary hover:bg-primary-dark text-white font-semibold transition-colors shadow-lg shadow-primary/20 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Adicionar Novo Golpe
            </button>
        </div>

        <!-- Grid de Cards de Golpes (3 por linha) -->
        <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($golpes as $golpe): ?>
            <div class="card overflow-hidden hover:shadow-xl transition-shadow duration-300">
                
                <!-- Header do Card com Imagem/Ícone -->
                <div class="relative h-32 bg-gradient-to-br from-<?= $golpe['elemento'] ?? 'gray' ?>-500/20 to-<?= $golpe['elemento'] ?? 'gray' ?>-600/10 flex items-center justify-center">
                    <?php if ($golpe['imagem']): ?>
                        <img src="<?= SITE_URL ?>/<?= $golpe['imagem'] ?>" alt="<?= $golpe['nome'] ?>" class="w-20 h-20 object-contain">
                    <?php else: ?>
                        <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/sword-clash.svg" alt="Golpe" class="w-16 h-16 opacity-30">
                    <?php endif; ?>
                    
                    <!-- Badge de Status -->
                    <div class="absolute top-3 right-3">
                        <?php if ($golpe['ativo']): ?>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-success/20 text-success">Ativo</span>
                        <?php else: ?>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-danger/20 text-danger">Inativo</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Conteúdo do Card -->
                <div class="p-5 space-y-4">
                    
                    <!-- Título e Tipo -->
                    <div>
                        <h3 class="font-title text-xl font-bold text-primary mb-2"><?= htmlspecialchars($golpe['nome']) ?></h3>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-lg text-xs font-semibold
                                <?php
                                switch($golpe['tipo']) {
                                    case 'ataque_fisico': echo 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'; break;
                                    case 'magia_ofensiva': echo 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'; break;
                                    case 'defesa': echo 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'; break;
                                    case 'cura': echo 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'; break;
                                    case 'suporte': echo 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'; break;
                                }
                                ?>">
                                <?= str_replace('_', ' ', ucfirst($golpe['tipo'])) ?>
                            </span>
                            
                            <?php if ($golpe['categoria']): ?>
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-primary/10 text-primary">
                                    <?= htmlspecialchars($golpe['categoria']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Descrição -->
                    <?php if ($golpe['descricao']): ?>
                    <p class="text-sm text-text/70 line-clamp-2">
                        <?= htmlspecialchars($golpe['descricao']) ?>
                    </p>
                    <?php endif; ?>

                    <!-- Informações em Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        
                        <!-- Dano -->
                        <div class="p-3 rounded-lg bg-rose-500/5 border border-rose-500/20">
                            <p class="text-xs text-text/60 mb-1">⚔️ Dano</p>
                            <p class="font-bold text-rose-600 dark:text-rose-400">
                                <?= $golpe['dano_base'] ?><?= $golpe['dano_extra'] > 0 ? ' +' . $golpe['dano_extra'] : '' ?>
                            </p>
                        </div>

                        <!-- Bônus Defesa -->
                        <div class="p-3 rounded-lg bg-blue-500/5 border border-blue-500/20">
                            <p class="text-xs text-text/60 mb-1">🛡️ Defesa</p>
                            <p class="font-bold text-blue-600 dark:text-blue-400">
                                +<?= $golpe['bonus_defesa'] ?? 0 ?>
                            </p>
                        </div>

                        <!-- Elemento -->
                        <div class="p-3 rounded-lg bg-purple-500/5 border border-purple-500/20">
                            <p class="text-xs text-text/60 mb-1">🔮 Elemento</p>
                            <p class="font-bold text-purple-600 dark:text-purple-400 capitalize">
                                <?= $golpe['elemento'] ?? 'Neutro' ?>
                            </p>
                        </div>

                        <!-- Nível -->
                        <div class="p-3 rounded-lg bg-amber-500/5 border border-amber-500/20">
                            <p class="text-xs text-text/60 mb-1">� Nível</p>
                            <span class="px-2 py-1 rounded text-xs font-semibold capitalize inline-block
                                <?php
                                switch($golpe['nivel_minimo']) {
                                    case 'principiante': echo 'bg-lime-100 text-lime-700 dark:bg-lime-900/30 dark:text-lime-300'; break;
                                    case 'experiente': echo 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'; break;
                                    case 'veterano': echo 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'; break;
                                    case 'mestre': echo 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'; break;
                                }
                                ?>">
                                <?= $golpe['nivel_minimo'] ?>
                            </span>
                        </div>

                        <!-- Usos -->
                        <div class="p-3 rounded-lg bg-cyan-500/5 border border-cyan-500/20">
                            <p class="text-xs text-text/60 mb-1">🔄 Usos Máx.</p>
                            <p class="font-bold text-cyan-600 dark:text-cyan-400">
                                <?= $golpe['usos_maximos'] ?>x
                            </p>
                        </div>

                    </div>

                    <!-- Informações Adicionais -->
                    <div class="space-y-2 pt-3 border-t border-primary/10">
                        
                        <?php if ($golpe['duracao_rodadas'] > 1): ?>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-text/60">⏱️ Duração:</span>
                            <span class="font-semibold text-primary"><?= $golpe['duracao_rodadas'] ?> rodadas</span>
                        </div>
                        <?php endif; ?>

                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-text/60">💰 Custo:</span>
                            <span class="font-semibold text-primary"><?= $golpe['custo_pontos'] ?? 1 ?> pts</span>
                        </div>

                        <div class="flex items-start gap-2 text-sm">
                            <span class="text-text/60 flex-shrink-0">🎯 Classes:</span>
                            <span class="font-semibold text-primary capitalize">
                                <?= $golpe['classes_permitidas'] ? htmlspecialchars($golpe['classes_permitidas']) : 'Todas' ?>
                            </span>
                        </div>

                        <div class="flex items-start gap-2 text-sm">
                            <span class="text-text/60 flex-shrink-0">📚 Habilidade:</span>
                            <span class="font-semibold text-primary">
                                <?= $golpe['habilidade_requerida_id'] ? 'ID #' . $golpe['habilidade_requerida_id'] : 'Nenhuma' ?>
                            </span>
                        </div>

                        <?php if ($golpe['efeitos_especiais']): ?>
                        <div class="text-sm pt-2 border-t border-primary/5">
                            <span class="text-text/60 font-semibold">✨ Efeitos:</span>
                            <p class="text-primary mt-1 text-xs leading-relaxed"><?= htmlspecialchars($golpe['efeitos_especiais']) ?></p>
                        </div>
                        <?php else: ?>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-text/60">✨ Efeitos:</span>
                            <span class="font-semibold text-text/40">Nenhum</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center gap-2 pt-3 border-t border-primary/10">
                        <button type="button" onclick="openModal(<?= $golpe['id'] ?>)" 
                                class="flex-1 px-4 py-2 rounded-lg bg-primary/10 hover:bg-primary/20 text-primary font-semibold transition-colors inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>
                        <button type="button" onclick="deleteGolpe(<?= $golpe['id'] ?>, '<?= htmlspecialchars($golpe['nome'], ENT_QUOTES) ?>')" 
                                class="flex-1 px-4 py-2 rounded-lg bg-danger/10 hover:bg-danger/20 text-danger font-semibold transition-colors inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Deletar
                        </button>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($golpes)): ?>
        <div class="card p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/sword-clash.svg" alt="Vazio" class="w-10 h-10 opacity-30">
            </div>
            <h3 class="font-title text-xl font-bold text-primary mb-2">Nenhum golpe cadastrado</h3>
            <p class="text-text/60 mb-4">Comece adicionando seu primeiro golpe especial!</p>
            <button onclick="alert('Modal de criação em breve!')" class="px-6 py-3 rounded-lg bg-primary hover:bg-primary-dark text-white font-semibold transition-colors shadow-lg shadow-primary/20">
                Adicionar Golpe
            </button>
        </div>
        <?php endif; ?>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
        <div class="card mt-6">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-text/60">
                        Mostrando <?= min($offset + 1, $totalGolpes) ?> - <?= min($offset + $itensPorPagina, $totalGolpes) ?> de <?= $totalGolpes ?> golpes
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- Botão Anterior -->
                        <?php if ($paginaAtual > 1): ?>
                        <a href="<?= $baseUrl ?>&p=<?= $paginaAtual - 1 ?>" 
                           class="px-4 py-2 rounded-lg bg-background-secondary/50 hover:bg-primary/10 text-text hover:text-primary transition-colors">
                            Anterior
                        </a>
                        <?php else: ?>
                        <span class="px-4 py-2 rounded-lg bg-background-secondary/30 text-text/30 cursor-not-allowed">
                            Anterior
                        </span>
                        <?php endif; ?>
                        
                        <!-- Números das páginas -->
                        <div class="flex items-center gap-1">
                            <?php
                            $range = 2; // Mostrar 2 páginas antes e depois
                            $start = max(1, $paginaAtual - $range);
                            $end = min($totalPaginas, $paginaAtual + $range);
                            
                            if ($start > 1): ?>
                                <a href="<?= $baseUrl ?>&p=1" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-primary/10 text-text hover:text-primary transition-colors">1</a>
                                <?php if ($start > 2): ?>
                                    <span class="px-2 text-text/30">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <?php if ($i == $paginaAtual): ?>
                                    <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-white font-bold">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= $baseUrl ?>&p=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-primary/10 text-text hover:text-primary transition-colors">
                                        <?= $i ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($end < $totalPaginas): ?>
                                <?php if ($end < $totalPaginas - 1): ?>
                                    <span class="px-2 text-text/30">...</span>
                                <?php endif; ?>
                                <a href="<?= $baseUrl ?>&p=<?= $totalPaginas ?>" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-primary/10 text-text hover:text-primary transition-colors">
                                    <?= $totalPaginas ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Botão Próximo -->
                        <?php if ($paginaAtual < $totalPaginas): ?>
                        <a href="<?= $baseUrl ?>&p=<?= $paginaAtual + 1 ?>" 
                           class="px-4 py-2 rounded-lg bg-background-secondary/50 hover:bg-primary/10 text-text hover:text-primary transition-colors">
                            Próximo
                        </a>
                        <?php else: ?>
                        <span class="px-4 py-2 rounded-lg bg-background-secondary/30 text-text/30 cursor-not-allowed">
                            Próximo
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<!-- Modal de Criar/Editar Golpe -->
<div id="golpeModal" class="modal fixed inset-0 bg-black/50 backdrop-blur-sm items-center justify-center p-4" style="display: none; z-index: 9999;">
    <div class="bg-background rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] overflow-hidden flex flex-col border-2 border-amber-200 dark:border-amber-600/30">
        
        <!-- Header do Modal -->
        <div class="flex items-center justify-between p-6 border-b border-primary/10 flex-shrink-0 bg-gradient-to-r from-primary/5 to-transparent">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                    <img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/sword-clash.svg" alt="Golpe" class="w-6 h-6">
                </div>
                <h2 id="modalTitle" class="font-title text-2xl font-bold text-primary">Novo Golpe Especial</h2>
            </div>
            <button onclick="closeModal()" class="text-text/60 hover:text-danger transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Formulário -->
        <form id="golpeForm" class="overflow-y-auto flex-1 p-6" onsubmit="saveGolpe(event)">
            <input type="hidden" id="golpe_id" name="id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Nome -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-text mb-2">
                        Nome do Golpe <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="nome" name="nome" required
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                           placeholder="Ex: Katon: Bola de Fogo">
                </div>

                <!-- Tipo -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Tipo <span class="text-danger">*</span>
                    </label>
                    <select id="tipo" name="tipo" required
                            class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="">Selecione...</option>
                        <option value="ataque_fisico">Ataque Físico</option>
                        <option value="magia_ofensiva">Magia Ofensiva</option>
                        <option value="defesa">Defesa</option>
                        <option value="cura">Cura</option>
                        <option value="suporte">Suporte</option>
                    </select>
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Categoria
                    </label>
                    <input type="text" id="categoria" name="categoria"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                           placeholder="Ex: Ninjutsu, Técnica Secreta">
                </div>

                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-text mb-2">
                        Descrição
                    </label>
                    <textarea id="descricao" name="descricao" rows="3"
                              class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                              placeholder="Descreva o golpe especial..."></textarea>
                </div>

                <!-- Dano Base -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Dano Base
                    </label>
                    <input type="number" id="dano_base" name="dano_base" min="0" value="0"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>

                <!-- Dano Extra -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Dano Extra
                    </label>
                    <input type="number" id="dano_extra" name="dano_extra" min="0" value="0"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>

                <!-- Bônus Defesa -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Bônus de Defesa
                    </label>
                    <input type="number" id="bonus_defesa" name="bonus_defesa" min="0" value="0"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    <p class="text-xs text-text/60 mt-1">Principalmente para golpes do tipo Defesa</p>
                </div>

                <!-- Duração em Rodadas -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Duração (Rodadas)
                    </label>
                    <input type="number" id="duracao_rodadas" name="duracao_rodadas" min="1" value="1"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>

                <!-- Elemento -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Elemento
                    </label>
                    <select id="elemento" name="elemento"
                            class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="">Nenhum/Neutro</option>
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

                <!-- Nível Mínimo -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Nível Mínimo
                    </label>
                    <select id="nivel_minimo" name="nivel_minimo"
                            class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="principiante">Principiante</option>
                        <option value="experiente">Experiente</option>
                        <option value="veterano">Veterano</option>
                        <option value="mestre">Mestre</option>
                    </select>
                </div>

                <!-- Classes Permitidas -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Classes Permitidas
                    </label>
                    <input type="text" id="classes_permitidas" name="classes_permitidas"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                           placeholder="Ex: Ninja, Samurai (separe por vírgula)">
                    <p class="text-xs text-text/60 mt-1">Deixe vazio para todas as classes</p>
                </div>

                <!-- Habilidade Requerida -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Habilidade Requerida
                    </label>
                    <select id="habilidade_requerida_id" name="habilidade_requerida_id"
                            class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="">Nenhuma</option>
                        <!-- Será preenchido via JavaScript -->
                    </select>
                </div>

                <!-- Usos Máximos -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Usos Máximos
                    </label>
                    <input type="number" id="usos_maximos" name="usos_maximos" min="1" value="3"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>

                <!-- Custo em Pontos -->
                <div>
                    <label class="block text-sm font-semibold text-text mb-2">
                        Custo em Pontos
                    </label>
                    <input type="number" id="custo_pontos" name="custo_pontos" min="1" value="1"
                           class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                </div>

                <!-- Efeitos Especiais -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-text mb-2">
                        Efeitos Especiais
                    </label>
                    <textarea id="efeitos_especiais" name="efeitos_especiais" rows="3"
                              class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                              placeholder="Descreva efeitos especiais como stun, queimadura, etc..."></textarea>
                </div>

                <!-- Imagem -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-text mb-2">
                        Imagem do Golpe
                    </label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <input type="file" id="imagem" name="imagem" accept="image/*"
                                   class="w-full px-4 py-2 rounded-lg bg-background-secondary border border-primary/20 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                                   onchange="previewImage(event)">
                            <p class="text-xs text-text/60 mt-1">PNG, JPG ou GIF - Máximo 2MB</p>
                        </div>
                        <div id="imagePreview" class="hidden w-24 h-24 rounded-lg border-2 border-primary/20 overflow-hidden bg-background-secondary">
                            <img id="previewImg" src="" alt="Preview" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <!-- Ativo -->
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="ativo" name="ativo" checked
                               class="w-5 h-5 rounded border-primary/20 text-primary focus:ring-2 focus:ring-primary/20">
                        <span class="text-sm font-semibold text-text">Golpe ativo e disponível</span>
                    </label>
                </div>

            </div>
        </form>

        <!-- Footer do Modal -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-primary/10 flex-shrink-0 bg-background-secondary/30">
            <button type="button" onclick="closeModal()" 
                    class="px-6 py-2 rounded-lg bg-background-secondary hover:bg-background-secondary/80 text-text font-semibold transition-colors">
                Cancelar
            </button>
            <button type="submit" form="golpeForm"
                    class="px-6 py-2 rounded-lg bg-primary hover:bg-primary-dark text-white font-semibold transition-colors shadow-lg shadow-primary/20">
                Salvar Golpe
            </button>
        </div>

    </div>
</div>

<script>
// Abrir modal
function openModal(golpeId = null) {
    const modal = document.getElementById('golpeModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('golpeForm');
    
    if (!modal) {
        console.error('Modal não encontrado!');
        return;
    }
    
    // Mostrar modal usando a classe 'show' e forçando display:flex
    modal.classList.add('show');
    modal.style.setProperty('display', 'flex', 'important');
    
    if (golpeId) {
        modalTitle.textContent = 'Editar Golpe Especial';
        loadGolpeData(golpeId);
    } else {
        modalTitle.textContent = 'Novo Golpe Especial';
        form.reset();
        document.getElementById('golpe_id').value = '';
        document.getElementById('imagePreview').classList.add('hidden');
    }
    
    loadHabilidades();
}

// Fechar modal
function closeModal() {
    const modal = document.getElementById('golpeModal');
    // Esconder modal removendo 'show' e forçando display:none
    modal.style.setProperty('display', 'none', 'important');
    modal.classList.remove('show');
    document.getElementById('golpeForm').reset();
}

// Preview da imagem
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

// Carregar habilidades disponíveis
function loadHabilidades() {
    fetch('/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/habilidades_handler.php?action=list')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('habilidade_requerida_id');
            // Manter a opção "Nenhuma"
            select.innerHTML = '<option value="">Nenhuma</option>';
            
            if (data.success && data.habilidades) {
                data.habilidades.forEach(hab => {
                    const option = document.createElement('option');
                    option.value = hab.id;
                    option.textContent = hab.nome;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Erro ao carregar habilidades:', error));
}

// Carregar dados do golpe para edição
function loadGolpeData(golpeId) {
    fetch(`/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/golpes_handler.php?action=get&id=${golpeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.golpe) {
                const golpe = data.golpe;
                
                // Preencher campos do formulário
                document.getElementById('golpe_id').value = golpe.id;
                document.getElementById('nome').value = golpe.nome || '';
                document.getElementById('tipo').value = golpe.tipo || '';
                document.getElementById('categoria').value = golpe.categoria || '';
                document.getElementById('descricao').value = golpe.descricao || '';
                document.getElementById('dano_base').value = golpe.dano_base || 0;
                document.getElementById('dano_extra').value = golpe.dano_extra || 0;
                document.getElementById('bonus_defesa').value = golpe.bonus_defesa || 0;
                document.getElementById('duracao_rodadas').value = golpe.duracao_rodadas || 1;
                document.getElementById('elemento').value = golpe.elemento || '';
                document.getElementById('nivel_minimo').value = golpe.nivel_minimo || 'principiante';
                document.getElementById('classes_permitidas').value = golpe.classes_permitidas || '';
                document.getElementById('habilidade_requerida_id').value = golpe.habilidade_requerida_id || '';
                document.getElementById('usos_maximos').value = golpe.usos_maximos || 3;
                document.getElementById('custo_pontos').value = golpe.custo_pontos || 1;
                document.getElementById('efeitos_especiais').value = golpe.efeitos_especiais || '';
                document.getElementById('ativo').checked = golpe.ativo == 1;
                
                // Mostrar preview da imagem se existir
                if (golpe.imagem) {
                    const imgUrl = `/Honra-e-Sombra/public/img/golpes/${golpe.imagem}`;
                    document.getElementById('previewImg').src = imgUrl;
                    document.getElementById('imagePreview').classList.remove('hidden');
                }
            } else {
                toast.error('Erro ao carregar dados do golpe', {
                    title: 'Erro'
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar golpe:', error);
            toast.error('Erro ao carregar dados do golpe', {
                title: 'Erro'
            });
        });
}

// Salvar golpe
function saveGolpe(event) {
    event.preventDefault();
    
    console.log('saveGolpe chamada');
    
    const formData = new FormData(document.getElementById('golpeForm'));
    const golpeId = document.getElementById('golpe_id').value;
    
    formData.append('action', golpeId ? 'update' : 'create');
    
    // Converter checkbox para 1 ou 0
    formData.set('ativo', document.getElementById('ativo').checked ? '1' : '0');
    
    // Debug - mostrar dados do form
    console.log('Dados do formulário:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    const url = '/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/golpes_handler.php';
    console.log('URL da requisição:', url);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Response text:', text);
        try {
            const data = JSON.parse(text);
            console.log('Response JSON:', data);
            
            if (data.success) {
                toast.success(data.message || 'Golpe salvo com sucesso!', {
                    title: 'Sucesso'
                });
                closeModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                toast.error(data.message || 'Erro ao salvar golpe', {
                    title: 'Erro'
                });
            }
        } catch (e) {
            console.error('Erro ao fazer parse do JSON:', e);
            console.error('Resposta recebida:', text);
            toast.error('Erro ao processar resposta do servidor', {
                title: 'Erro'
            });
        }
    })
    .catch(error => {
        console.error('Erro na requisição:', error);
        toast.error('Erro ao processar requisição', {
            title: 'Erro'
        });
    });
}

// Deletar golpe
function deleteGolpe(golpeId, golpeNome) {
    if (!confirm(`Tem certeza que deseja deletar o golpe "${golpeNome}"?\n\nEsta ação não pode ser desfeita.`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', golpeId);
    
    fetch('/Honra-e-Sombra/app/views/admin/content-management-rpg/handlers/golpes_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toast.success(data.message || 'Golpe deletado com sucesso!', {
                title: 'Sucesso'
            });
            setTimeout(() => location.reload(), 1500);
        } else {
            toast.error(data.message || 'Erro ao deletar golpe', {
                title: 'Erro'
            });
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        toast.error('Erro ao processar requisição', {
            title: 'Erro'
        });
    });
}

// Fechar modal ao clicar fora
document.getElementById('golpeModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
