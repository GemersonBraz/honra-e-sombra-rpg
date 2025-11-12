-- ============================================
-- Migration 003: Sistema de Dados Base
-- Descrição: Classes, Habilidades, Golpes Templates e Elementos
-- Data: 2025-11-11
-- ============================================

-- Tabela de Classes
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT,
    hp_base INT NOT NULL DEFAULT 100,
    atk_base INT NOT NULL DEFAULT 10,
    def_base INT NOT NULL DEFAULT 10,
    elemento_afinidade ENUM('fogo', 'agua', 'terra', 'ar', 'luz', 'trevas', 'raio', 'gelo', 'natureza'),
    armas_permitidas TEXT COMMENT 'JSON array com tipos de armas',
    especialidade VARCHAR(100),
    bonus_especial TEXT COMMENT 'Descrição do bônus único da classe',
    imagem VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_nome (nome),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Elementos (características e interações)
CREATE TABLE IF NOT EXISTS elementos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome ENUM('fogo', 'agua', 'terra', 'ar', 'luz', 'trevas', 'raio', 'gelo', 'natureza') NOT NULL UNIQUE,
    descricao TEXT,
    cor_hex VARCHAR(7) COMMENT 'Cor para UI',
    icone VARCHAR(100),
    forte_contra VARCHAR(255) COMMENT 'Elementos que este é forte (separados por vírgula)',
    fraco_contra VARCHAR(255) COMMENT 'Elementos que este é fraco (separados por vírgula)',
    bonus_dano_percentual INT DEFAULT 50 COMMENT 'Percentual de bônus quando forte contra',
    
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Habilidades Disponíveis (catálogo)
CREATE TABLE IF NOT EXISTS habilidades_disponiveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    categoria ENUM('magia', 'tecnica_arma', 'fisico', 'mental') NOT NULL,
    tipo VARCHAR(50) COMMENT 'Subtipo específico (ex: ninjutsu, genjutsu, taijutsu)',
    descricao TEXT,
    nivel_minimo ENUM('principiante', 'experiente', 'veterano', 'mestre') DEFAULT 'principiante',
    classes_permitidas VARCHAR(255) COMMENT 'Classes que podem aprender (null = todas)',
    elemento_requerido ENUM('fogo', 'agua', 'terra', 'ar', 'luz', 'trevas', 'raio', 'gelo', 'natureza'),
    bonus_atk INT DEFAULT 0,
    bonus_def INT DEFAULT 0,
    bonus_hp INT DEFAULT 0,
    efeito_especial TEXT COMMENT 'Descrição de efeitos especiais',
    custo_pontos INT DEFAULT 1 COMMENT 'Quantos pontos custa para aprender',
    prerequisito_habilidade_id INT COMMENT 'ID de habilidade necessária antes desta',
    imagem VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    criado_por_admin INT COMMENT 'ID do admin que criou (null = sistema)',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (prerequisito_habilidade_id) REFERENCES habilidades_disponiveis(id) ON DELETE SET NULL,
    
    INDEX idx_categoria (categoria),
    INDEX idx_nivel (nivel_minimo),
    INDEX idx_ativo (ativo),
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Golpes Templates (catálogo de golpes que jogadores podem aprender/criar)
CREATE TABLE IF NOT EXISTS golpes_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    tipo ENUM('ataque_fisico', 'magia_ofensiva', 'defesa', 'cura', 'suporte') NOT NULL,
    categoria VARCHAR(50) COMMENT 'Categoria específica (ex: katon, suiton, taijutsu)',
    descricao TEXT,
    dano_base INT DEFAULT 0,
    dano_extra INT DEFAULT 0,
    duracao_rodadas INT DEFAULT 1,
    elemento ENUM('fogo', 'agua', 'terra', 'ar', 'luz', 'trevas', 'raio', 'gelo', 'natureza'),
    nivel_minimo ENUM('principiante', 'experiente', 'veterano', 'mestre') DEFAULT 'principiante',
    classes_permitidas VARCHAR(255) COMMENT 'Classes que podem usar (null = todas)',
    habilidade_requerida_id INT COMMENT 'ID da habilidade necessária',
    usos_maximos INT DEFAULT 3 COMMENT 'Usos por combate/dia',
    custo_pontos INT DEFAULT 1 COMMENT 'Pontos gastos para criar/aprender',
    efeitos_especiais TEXT COMMENT 'Buffs, debuffs, condições especiais',
    imagem VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    criado_por_admin INT COMMENT 'ID do admin que criou (null = sistema)',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (habilidade_requerida_id) REFERENCES habilidades_disponiveis(id) ON DELETE SET NULL,
    
    INDEX idx_tipo (tipo),
    INDEX idx_nivel (nivel_minimo),
    INDEX idx_elemento (elemento),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERIR DADOS INICIAIS - CLASSES
-- ============================================

INSERT INTO classes (nome, descricao, hp_base, atk_base, def_base, elemento_afinidade, especialidade, bonus_especial) VALUES
('ninja', 'Mestre da velocidade e furtividade, especializado em técnicas de ocultação e ataques rápidos.', 100, 12, 8, 'ar', 'Furtividade e Velocidade', 'Pode usar 2 ações rápidas por turno'),
('samurai', 'Guerreiro honrado que domina a arte da espada e técnicas de combate disciplinadas.', 120, 15, 12, 'fogo', 'Maestria com Espadas', '+3 ATK quando usa katanas'),
('guerreiro', 'Combatente versátil e resistente, capaz de usar qualquer arma e armadura pesada.', 140, 13, 15, 'terra', 'Versatilidade em Combate', 'Pode equipar qualquer arma sem penalidade'),
('cavaleiro', 'Defensor nobre montado, especializado em cargas devastadoras e proteção de aliados.', 130, 14, 16, 'luz', 'Defesa de Aliados', '+5 DEF quando protege aliado'),
('cacador', 'Rastreador especializado em combate à distância e sobrevivência na natureza.', 100, 16, 9, 'natureza', 'Combate à Distância', 'Ataque crítico dobrado com arcos'),
('assassino', 'Especialista letal em eliminação silenciosa e golpes críticos devastadores.', 90, 18, 7, 'trevas', 'Golpes Críticos', 'Chance crítica +30%'),
('guardiao', 'Protetor implacável focado em defesa absoluta e resistência prolongada.', 160, 10, 18, 'terra', 'Defesa Absoluta', 'Pode bloquear ataques de aliados próximos'),
('mago', 'Estudioso das artes arcanas capaz de conjurar magias poderosas de diversos elementos.', 80, 8, 6, 'raio', 'Maestria Arcana', 'Pode usar 2 elementos diferentes'),
('monge', 'Lutador disciplinado que domina artes marciais e energia espiritual.', 110, 14, 11, 'luz', 'Artes Marciais', 'Dano aumenta 50% quando desarmado');

-- ============================================
-- INSERIR DADOS INICIAIS - ELEMENTOS
-- ============================================

INSERT INTO elementos (nome, descricao, cor_hex, icone, forte_contra, fraco_contra, bonus_dano_percentual) VALUES
('fogo', 'Elemento de paixão e destruição, causa dano contínuo e ignora armaduras leves.', '#FF4500', '🔥', 'ar,gelo', 'agua,terra', 50),
('agua', 'Elemento fluido e adaptável, cura ferimentos e pode congelar inimigos.', '#1E90FF', '💧', 'fogo,terra', 'raio,ar', 50),
('terra', 'Elemento sólido e resistente, aumenta defesa e causa dano esmagador.', '#8B4513', '🪨', 'raio,ar', 'agua,natureza', 50),
('ar', 'Elemento veloz e evasivo, aumenta velocidade e permite ataques em área.', '#87CEEB', '💨', 'terra,agua', 'fogo,raio', 50),
('luz', 'Elemento sagrado de cura e purificação, eficaz contra trevas e mortos-vivos.', '#FFD700', '✨', 'trevas', 'trevas', 75),
('trevas', 'Elemento sombrio de maldições e absorção de vida.', '#2F004F', '🌑', 'luz', 'luz', 75),
('raio', 'Elemento elétrico de velocidade e precisão, causa paralisia e dano crítico.', '#FFFF00', '⚡', 'agua,ar', 'terra', 60),
('gelo', 'Elemento congelante que reduz velocidade e aumenta fragilidade.', '#00FFFF', '❄️', 'agua,ar', 'fogo', 50),
('natureza', 'Elemento vital de crescimento, cura e controle de plantas/animais.', '#228B22', '🌿', 'terra,agua', 'fogo', 40);

-- ============================================
-- INSERIR DADOS INICIAIS - HABILIDADES DISPONÍVEIS
-- ============================================

-- Habilidades de Ninja
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Substituição', 'tecnica_arma', 'ninjutsu', 'Troca de lugar com um objeto próximo para evitar ataque.', 'principiante', 'ninja', 0, 5, 1),
('Clone das Sombras', 'magia', 'ninjutsu', 'Cria clones temporários para confundir inimigos.', 'experiente', 'ninja', 3, 0, 2),
('Ocultação Avançada', 'mental', 'furtividade', 'Torna-se completamente invisível por 3 turnos.', 'veterano', 'ninja', 0, 0, 2),
('Selamento', 'magia', 'fuinjutsu', 'Sela chakra ou movimento do inimigo.', 'mestre', 'ninja', 0, 0, 3);

-- Habilidades de Samurai
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Golpe Iai', 'tecnica_arma', 'kenjutsu', 'Desembainha e ataca em movimento único devastador.', 'principiante', 'samurai', 5, 0, 1),
('Postura de Ferro', 'fisico', 'defesa', 'Aumenta drasticamente defesa por 2 turnos.', 'experiente', 'samurai', 0, 8, 2),
('Corte Celestial', 'tecnica_arma', 'kenjutsu', 'Corte poderoso que ignora armadura.', 'veterano', 'samurai', 10, 0, 2),
('Código Bushido', 'mental', 'honra', 'Sacrifica HP para aumentar ATK permanentemente.', 'mestre', 'samurai', 15, -5, 3);

-- Habilidades de Guerreiro
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Investida Brutal', 'fisico', 'ataque', 'Carga devastadora que atordoa inimigo.', 'principiante', 'guerreiro', 4, 0, 1),
('Bloqueio Perfeito', 'tecnica_arma', 'defesa', 'Bloqueia próximo ataque completamente.', 'experiente', 'guerreiro', 0, 10, 2),
('Fúria de Batalha', 'fisico', 'buff', 'Aumenta ATK mas reduz DEF por 5 turnos.', 'veterano', 'guerreiro', 12, -5, 2),
('Última Resistência', 'mental', 'sobrevivencia', 'Sobrevive com 1 HP a golpe letal uma vez por combate.', 'mestre', 'guerreiro', 0, 0, 3);

-- Habilidades de Mago
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, elemento_requerido, bonus_atk, custo_pontos) VALUES
('Bola de Fogo', 'magia', 'evocacao', 'Conjura bola de fogo explosiva.', 'principiante', 'mago', 'fogo', 6, 1),
('Escudo Arcano', 'magia', 'abjuracao', 'Cria barreira mágica que absorve dano.', 'experiente', 'mago', NULL, 0, 2),
('Raio Congelante', 'magia', 'evocacao', 'Dispara raio de gelo que paralisa.', 'veterano', 'mago', 'gelo', 8, 2),
('Meteoro', 'magia', 'evocacao', 'Invoca meteoro massivo em área.', 'mestre', 'mago', 'fogo', 20, 3);

-- Habilidades de Monge
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_hp, custo_pontos) VALUES
('Punho de Ferro', 'fisico', 'taijutsu', 'Soco poderoso que quebra defesas.', 'principiante', 'monge', 5, 0, 1),
('Meditação Curativa', 'mental', 'cura', 'Recupera HP meditando.', 'experiente', 'monge', 0, 30, 2),
('Chakra Explosivo', 'fisico', 'taijutsu', 'Libera energia em explosão ao redor.', 'veterano', 'monge', 8, 0, 2),
('Nirvana', 'mental', 'espiritual', 'Estado elevado que dobra todos os atributos por 3 turnos.', 'mestre', 'monge', 10, 50, 3);

-- Habilidades de Caçador
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, custo_pontos) VALUES
('Tiro Preciso', 'tecnica_arma', 'arco', 'Mira perfeita que sempre acerta ponto vital.', 'principiante', 'cacador', 4, 1),
('Armadilha Oculta', 'mental', 'estrategia', 'Coloca armadilha que imobiliza inimigo.', 'experiente', 'cacador', 0, 2),
('Flecha Perfurante', 'tecnica_arma', 'arco', 'Flecha que atravessa múltiplos alvos.', 'veterano', 'cacador', 10, 2),
('Marca do Caçador', 'mental', 'rastreamento', 'Marca alvo para dano dobrado por 5 turnos.', 'mestre', 'cacador', 15, 3);

-- Habilidades de Assassino
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, custo_pontos) VALUES
('Ataque Furtivo', 'tecnica_arma', 'furtividade', 'Ataque pelas costas com dano triplicado.', 'principiante', 'assassino', 8, 1),
('Veneno Letal', 'mental', 'alquimia', 'Aplica veneno que causa dano contínuo.', 'experiente', 'assassino', 0, 2),
('Dança das Lâminas', 'tecnica_arma', 'dupla_adaga', 'Sequência rápida de 5 ataques.', 'veterano', 'assassino', 12, 2),
('Execução', 'mental', 'assassinato', 'Mata instantaneamente inimigo com menos de 20% HP.', 'mestre', 'assassino', 0, 3);

-- Habilidades de Cavaleiro
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_def, custo_pontos) VALUES
('Carga Montada', 'tecnica_arma', 'cavalaria', 'Ataque devastador com lança montado.', 'principiante', 'cavaleiro', 0, 1),
('Proteção Divina', 'magia', 'luz', 'Protege aliado com escudo de luz.', 'experiente', 'cavaleiro', 10, 2),
('Aura de Coragem', 'mental', 'lideranca', 'Aumenta ATK e DEF de aliados próximos.', 'veterano', 'cavaleiro', 5, 2),
('Sacrifício Nobre', 'mental', 'honra', 'Transfere todo dano de aliado para si.', 'mestre', 'cavaleiro', 15, 3);

-- Habilidades de Guardião
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_def, bonus_hp, custo_pontos) VALUES
('Muralha Viva', 'fisico', 'defesa', 'Torna-se barreira impenetrável.', 'principiante', 'guardiao', 8, 20, 1),
('Provocação', 'mental', 'taunt', 'Força todos inimigos a atacarem você.', 'experiente', 'guardiao', 5, 0, 2),
('Fortificação', 'fisico', 'defesa', 'DEF aumenta a cada turno até +20.', 'veterano', 'guardiao', 10, 30, 2),
('Bastião Imortal', 'mental', 'sobrevivencia', 'Imune a dano por 2 turnos, mas não pode atacar.', 'mestre', 'guardiao', 20, 50, 3);

-- ============================================
-- INSERIR DADOS INICIAIS - GOLPES TEMPLATES
-- ============================================

-- Golpes de Fogo
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Katon: Grande Bola de Fogo', 'magia_ofensiva', 'katon', 'Dispara enorme bola de fogo que causa queimadura.', 30, 10, 1, 'fogo', 'experiente', 'ninja,mago', 3, 2),
('Espada Flamejante', 'ataque_fisico', 'kenjutsu', 'Envolve lâmina em chamas para corte incandescente.', 25, 15, 1, 'fogo', 'veterano', 'samurai,guerreiro', 4, 2),
('Punho Explosivo', 'ataque_fisico', 'taijutsu', 'Soco carregado com chakra explosivo.', 20, 20, 1, 'fogo', 'experiente', 'monge', 5, 2);

-- Golpes de Água
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Suiton: Dragão Aquático', 'magia_ofensiva', 'suiton', 'Cria dragão de água que ataca inimigo.', 28, 12, 1, 'agua', 'veterano', 'ninja,mago', 3, 2),
('Tsunami', 'magia_ofensiva', 'suiton', 'Onda massiva que atinge todos inimigos.', 35, 10, 1, 'agua', 'mestre', 'mago', 2, 3),
('Chuva de Cura', 'cura', 'suiton', 'Chuva suave que restaura HP de aliados.', 0, 40, 1, 'agua', 'experiente', 'mago,monge', 3, 2);

-- Golpes de Terra
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Doton: Muro de Terra', 'defesa', 'doton', 'Ergue muralha que bloqueia ataques.', 0, 0, 3, 'terra', 'experiente', 'ninja,guerreiro', 4, 2),
('Terremoto', 'magia_ofensiva', 'doton', 'Abala chão causando dano e derrubando inimigos.', 30, 0, 1, 'terra', 'veterano', 'mago,guardiao', 3, 2),
('Prisão de Pedra', 'suporte', 'doton', 'Aprisiona inimigo em rocha sólida.', 15, 0, 2, 'terra', 'mestre', 'mago', 2, 3);

-- Golpes de Raio
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Raiton: Raio Veloz', 'magia_ofensiva', 'raiton', 'Dispara raio em linha reta que paralisa.', 32, 8, 1, 'raio', 'experiente', 'ninja,mago', 4, 2),
('Chidori', 'ataque_fisico', 'raiton', 'Concentra chakra de raio na mão para perfurar.', 40, 20, 1, 'raio', 'mestre', 'ninja', 2, 3),
('Tempestade Elétrica', 'magia_ofensiva', 'raiton', 'Chuva de raios atinge área grande.', 35, 15, 1, 'raio', 'mestre', 'mago', 2, 3);

-- Golpes de Vento/Ar
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Fuuton: Lâmina de Vento', 'magia_ofensiva', 'fuuton', 'Corte invisível de vento afiado.', 28, 12, 1, 'ar', 'experiente', 'ninja,mago', 4, 2),
('Redemoinho', 'magia_ofensiva', 'fuuton', 'Tornado que suga e arremessa inimigos.', 25, 10, 1, 'ar', 'veterano', 'mago', 3, 2),
('Voo Rápido', 'suporte', 'fuuton', 'Usa vento para aumentar velocidade drasticamente.', 0, 0, 3, 'ar', 'experiente', 'ninja,cacador', 5, 2);

-- Golpes de Luz
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Raio Sagrado', 'magia_ofensiva', 'luz', 'Feixe de luz divina que purifica.', 30, 15, 1, 'luz', 'experiente', 'mago,cavaleiro,monge', 4, 2),
('Cura Maior', 'cura', 'luz', 'Restaura grande quantidade de HP.', 0, 60, 1, 'luz', 'veterano', 'mago,monge', 3, 2),
('Benção Divina', 'suporte', 'luz', 'Aumenta todos atributos de aliado.', 0, 0, 3, 'luz', 'mestre', 'cavaleiro,mago', 2, 3);

-- Golpes de Trevas
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Sombra Cortante', 'magia_ofensiva', 'trevas', 'Lâmina de sombra que drena vida.', 25, 20, 1, 'trevas', 'experiente', 'assassino,mago', 4, 2),
('Maldição', 'suporte', 'trevas', 'Amaldiçoa inimigo reduzindo seus atributos.', 10, 0, 5, 'trevas', 'veterano', 'mago,assassino', 3, 2),
('Abismo Negro', 'magia_ofensiva', 'trevas', 'Cria buraco negro que absorve tudo.', 45, 25, 1, 'trevas', 'mestre', 'mago', 2, 3);

-- Golpes de Gelo
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Hyouton: Prisão de Gelo', 'suporte', 'hyouton', 'Congela inimigo imobilizando-o.', 15, 0, 2, 'gelo', 'experiente', 'mago', 4, 2),
('Lança de Gelo', 'magia_ofensiva', 'hyouton', 'Estaca de gelo perfura e congela.', 30, 10, 1, 'gelo', 'veterano', 'mago', 3, 2),
('Nevasca Eterna', 'magia_ofensiva', 'hyouton', 'Tempestade de gelo em área massiva.', 35, 15, 1, 'gelo', 'mestre', 'mago', 2, 3);

-- Golpes de Natureza
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, elemento, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Invocação: Lobo', 'suporte', 'invocacao', 'Invoca lobo para lutar ao seu lado.', 20, 0, 5, 'natureza', 'experiente', 'cacador,ninja', 3, 2),
('Regeneração Natural', 'cura', 'natureza', 'Cura contínua por 5 turnos.', 0, 0, 5, 'natureza', 'veterano', 'mago,monge', 3, 2),
('Fúria da Floresta', 'magia_ofensiva', 'natureza', 'Árvores e plantas atacam inimigos.', 35, 0, 1, 'natureza', 'mestre', 'mago', 2, 3);

-- Golpes Físicos (sem elemento)
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, duracao_rodadas, nivel_minimo, classes_permitidas, usos_maximos, custo_pontos) VALUES
('Golpe Mortal', 'ataque_fisico', 'combate', 'Ataque poderoso concentrado.', 35, 20, 1, 'experiente', NULL, 4, 2),
('Contra-Ataque', 'defesa', 'combate', 'Defende e revida com força dobrada.', 20, 20, 1, 'veterano', 'samurai,guerreiro,cavaleiro', 3, 2),
('Corte Giratório', 'ataque_fisico', 'combate', 'Giro de 360° atingindo todos ao redor.', 30, 15, 1, 'experiente', 'samurai,guerreiro', 3, 2),
('Decapitação', 'ataque_fisico', 'execucao', 'Golpe letal com chance de morte instantânea.', 50, 30, 1, 'mestre', 'samurai,assassino', 1, 3);

-- ============================================
-- FIM DA MIGRATION 003
-- ============================================
