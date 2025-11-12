-- Migration 006: Create Weapons and Equipments System
-- Criação das tabelas de armas e equipamentos para a loja

-- Tabela de Armas
CREATE TABLE IF NOT EXISTS weapons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    tipo ENUM('arma_leve', 'arma_pesada', 'duas_armas', 'arma_especial', 'escudo', 'sem_categoria') DEFAULT 'sem_categoria',
    atk_bonus INT DEFAULT 0 COMMENT 'Bônus de ataque que a arma fornece',
    def_bonus INT DEFAULT 0 COMMENT 'Bônus de defesa que a arma fornece',
    preco DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Preço da arma na loja',
    raridade ENUM('comum', 'incomum', 'raro', 'epico', 'lendario') DEFAULT 'comum',
    nivel_minimo INT DEFAULT 1 COMMENT 'Nível mínimo para equipar',
    durabilidade_max INT DEFAULT 100 COMMENT 'Durabilidade máxima da arma',
    peso INT DEFAULT 1 COMMENT 'Peso da arma (afeta carga)',
    classes_permitidas TEXT COMMENT 'Array JSON com classes que podem usar ["ninja", "samurai"]',
    elemento_afinidade VARCHAR(50) COMMENT 'Elemento associado à arma',
    efeito_especial TEXT COMMENT 'Descrição de efeitos especiais',
    imagem VARCHAR(255) COMMENT 'Caminho da imagem da arma',
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_preco (preco),
    INDEX idx_raridade (raridade),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Equipamentos (armaduras, acessórios, consumíveis)
CREATE TABLE IF NOT EXISTS equipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    tipo ENUM('armadura', 'acessorio', 'consumivel', 'ferramenta', 'outro') DEFAULT 'outro',
    subtipo VARCHAR(50) COMMENT 'Ex: capacete, botas, poção, granada, etc',
    atk_bonus INT DEFAULT 0 COMMENT 'Bônus de ataque',
    def_bonus INT DEFAULT 0 COMMENT 'Bônus de defesa',
    hp_bonus INT DEFAULT 0 COMMENT 'Bônus de HP',
    efeito_especial TEXT COMMENT 'Descrição de efeitos (ex: recupera +20 HP, +10 ATK temporário)',
    preco DECIMAL(10, 2) DEFAULT 0.00,
    raridade ENUM('comum', 'incomum', 'raro', 'epico', 'lendario') DEFAULT 'comum',
    nivel_minimo INT DEFAULT 1,
    durabilidade_max INT DEFAULT 100 COMMENT 'Para armaduras, NULL para consumíveis',
    consumivel BOOLEAN DEFAULT FALSE COMMENT 'Se é um item de uso único',
    quantidade_uso INT DEFAULT 1 COMMENT 'Quantidade de usos (para consumíveis)',
    peso INT DEFAULT 1,
    classes_permitidas TEXT COMMENT 'Classes que podem usar, NULL = todas',
    slot_equipamento VARCHAR(50) COMMENT 'cabeça, corpo, pernas, mãos, pés, acessório, etc',
    imagem VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_preco (preco),
    INDEX idx_raridade (raridade),
    INDEX idx_consumivel (consumivel),
    INDEX idx_ativo (ativo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir algumas armas iniciais baseadas no documento de regras

-- Armas Ninja
INSERT INTO weapons (nome, tipo, atk_bonus, def_bonus, preco, raridade, classes_permitidas, descricao) VALUES
('Espada de Treino', 'sem_categoria', 2, 2, 13.00, 'comum', '["ninja", "samurai", "guerreiro", "cavaleiro", "guardiao", "andarilho", "clerigo", "mago", "monge"]', 'Espada básica de treinamento'),
('Espada de Madeira', 'arma_leve', 3, 2, 20.00, 'comum', '["ninja", "samurai", "guerreiro", "cavaleiro", "guardiao", "andarilho", "monge"]', 'Espada de madeira para iniciantes'),
('Katana', 'arma_leve', 5, 4, 40.00, 'incomum', '["ninja", "samurai"]', 'Espada tradicional japonesa'),
('Katana Dupla', 'duas_armas', 8, 6, 65.00, 'raro', '["ninja", "samurai"]', 'Par de katanas para combate duplo'),
('Sabre', 'arma_leve', 4, 5, 40.00, 'incomum', '["ninja", "samurai", "guerreiro", "cavaleiro", "guardiao", "andarilho"]', 'Sabre curvo de combate'),
('Espada Curta', 'arma_leve', 4, 3, 35.00, 'comum', '["ninja", "samurai", "guerreiro", "cavaleiro", "guardiao", "andarilho"]', 'Espada de tamanho reduzido'),
('Espada Curta Dupla', 'duas_armas', 6, 4, 55.00, 'incomum', '["ninja", "guerreiro", "andarilho"]', 'Par de espadas curtas'),
('Espada Ninja', 'arma_leve', 7, 5, 85.00, 'raro', '["ninja"]', 'Espada especializada ninja'),
('Espada Shinobi', 'arma_pesada', 10, 8, 100.00, 'epico', '["ninja"]', 'Lendária espada shinobi'),
('Bastão de Metal', 'arma_pesada', 6, 6, 60.00, 'incomum', '["ninja", "monge", "clerigo"]', 'Bastão resistente de metal'),
('Tridente Duplo', 'arma_pesada', 9, 5, 80.00, 'raro', '["ninja", "guerreiro"]', 'Tridente duplo para combate'),
('Sai', 'arma_especial', 4, 2, 40.00, 'incomum', '["ninja"]', 'Arma tradicional de defesa'),
('Sai Duplo', 'duas_armas', 6, 3, 60.00, 'raro', '["ninja"]', 'Par de sais'),
('Foice Curta', 'arma_leve', 5, 3, 50.00, 'incomum', '["ninja", "andarilho"]', 'Foice pequena de combate'),
('Foice Curta com Corrente', 'arma_especial', 7, 4, 60.00, 'raro', '["ninja"]', 'Foice com corrente kusarigama');

-- Armas Samurai
INSERT INTO weapons (nome, tipo, atk_bonus, def_bonus, preco, raridade, classes_permitidas, descricao) VALUES
('Sakabatou', 'arma_leve', 6, 7, 80.00, 'raro', '["samurai"]', 'Espada de lâmina invertida'),
('Taichi', 'arma_leve', 7, 4, 80.00, 'raro', '["samurai"]', 'Espada tradicional chinesa'),
('Uchigatana', 'arma_leve', 7, 6, 80.00, 'raro', '["samurai"]', 'Variação da katana'),
('Zulfigar', 'arma_leve', 6, 5, 60.00, 'incomum', '["samurai", "guerreiro"]', 'Espada de lâmina bifurcada'),
('Espada Simples', 'arma_leve', 3, 3, 30.00, 'comum', '["samurai", "guerreiro", "cavaleiro", "guardiao", "andarilho"]', 'Espada básica'),
('Honjo Masamune', 'arma_pesada', 9, 8, 100.00, 'lendario', '["samurai"]', 'Lendária espada Masamune'),
('Claymore', 'arma_pesada', 10, 8, 85.00, 'epico', '["samurai", "guerreiro", "cavaleiro"]', 'Grande espada escocesa'),
('Espada Longa', 'arma_pesada', 7, 5, 85.00, 'raro', '["samurai", "guerreiro", "cavaleiro", "guardiao"]', 'Espada de lâmina longa'),
('Espada Larga', 'arma_pesada', 7, 5, 85.00, 'raro', '["samurai", "guerreiro", "cavaleiro"]', 'Espada de lâmina larga'),
('Wakizashi', 'arma_leve', 5, 4, 50.00, 'incomum', '["samurai"]', 'Espada curta samurai'),
('Kodashi', 'arma_leve', 3, 5, 50.00, 'incomum', '["samurai"]', 'Espada curta de defesa'),
('Kodashi Dupla', 'duas_armas', 6, 8, 70.00, 'raro', '["samurai"]', 'Par de kodashis'),
('Espada Musashi', 'arma_pesada', 11, 6, 100.00, 'lendario', '["samurai"]', 'Espada do lendário Musashi');

-- Armas Guerreiro
INSERT INTO weapons (nome, tipo, atk_bonus, def_bonus, preco, raridade, classes_permitidas, descricao) VALUES
('Alabarda - Naginata', 'arma_pesada', 7, 6, 90.00, 'raro', '["guerreiro", "guardiao"]', 'Lança longa com lâmina'),
('Tonfa', 'arma_especial', 2, 7, 65.00, 'incomum', '["guerreiro", "monge", "ninja"]', 'Bastão de defesa'),
('Facão Espada', 'arma_leve', 6, 4, 60.00, 'incomum', '["guerreiro", "andarilho"]', 'Espada larga tipo facão'),
('Cimitarra', 'arma_leve', 6, 5, 50.00, 'incomum', '["guerreiro", "andarilho"]', 'Espada curva do oriente'),
('Espada de Duas Mãos', 'arma_pesada', 8, 7, 95.00, 'raro', '["guerreiro", "cavaleiro"]', 'Grande espada a duas mãos'),
('Espada Bastarda', 'arma_pesada', 10, 7, 110.00, 'epico', '["guerreiro", "cavaleiro"]', 'Espada versátil poderosa'),
('Zambatou', 'arma_pesada', 13, 3, 130.00, 'lendario', '["guerreiro"]', 'Espada gigante de combate'),
('Porrete', 'sem_categoria', 4, 3, 45.00, 'comum', '["guerreiro", "monge", "andarilho"]', 'Porrete simples'),
('Porrete Maça', 'arma_pesada', 5, 3, 50.00, 'incomum', '["guerreiro", "clerigo"]', 'Maça de combate'),
('Machado de Dois Fios', 'arma_pesada', 8, 4, 100.00, 'epico', '["guerreiro"]', 'Machado duplo devastador'),
('Luva de Combate', 'arma_leve', 3, 1, 30.00, 'comum', '["guerreiro", "monge"]', 'Luvas reforçadas');

-- Escudos
INSERT INTO weapons (nome, tipo, atk_bonus, def_bonus, preco, raridade, classes_permitidas, descricao) VALUES
('Escudo Simples', 'escudo', 0, 4, 50.00, 'comum', '["guerreiro", "cavaleiro", "guardiao"]', 'Escudo básico de madeira'),
('Escudo Melhorado', 'escudo', 0, 6, 55.00, 'incomum', '["guerreiro", "cavaleiro", "guardiao"]', 'Escudo reforçado'),
('Escudo de Guerra', 'escudo', 0, 9, 110.00, 'epico', '["guerreiro", "cavaleiro", "guardiao"]', 'Grande escudo de guerra');

-- Inserir alguns equipamentos iniciais

-- Armaduras Ninja
INSERT INTO equipments (nome, tipo, subtipo, atk_bonus, def_bonus, preco, raridade, classes_permitidas, slot_equipamento, descricao) VALUES
('Braceletes de Metal', 'armadura', 'braços', 0, 3, 10.00, 'comum', '["ninja"]', 'braços', 'Proteção para os braços'),
('Máscara de Metal', 'armadura', 'cabeça', 0, 3, 10.00, 'comum', '["ninja"]', 'cabeça', 'Máscara protetora'),
('Armadura Básica', 'armadura', 'corpo', 0, 5, 65.00, 'comum', '["ninja", "samurai", "guerreiro"]', 'corpo', 'Armadura simples'),
('Roupa de Couro Especial', 'armadura', 'corpo', 0, 3, 45.00, 'comum', '["ninja", "samurai", "andarilho"]', 'corpo', 'Roupa leve de couro'),
('Armadura Especial Ninja', 'armadura', 'corpo', 0, 7, 100.00, 'raro', '["ninja"]', 'corpo', 'Armadura ninja avançada'),
('Armadura Shinobi', 'armadura', 'corpo', -1, 9, 120.00, 'epico', '["ninja"]', 'corpo', 'Armadura shinobi pesada');

-- Armaduras Samurai
INSERT INTO equipments (nome, tipo, subtipo, atk_bonus, def_bonus, preco, raridade, classes_permitidas, slot_equipamento, descricao) VALUES
('Máscara', 'armadura', 'cabeça', 0, 2, 10.00, 'comum', '["samurai", "ninja"]', 'cabeça', 'Máscara simples'),
('Armadura Especial', 'armadura', 'corpo', 0, 6, 100.00, 'raro', '["samurai"]', 'corpo', 'Armadura samurai especial'),
('Armadura Musashi', 'armadura', 'corpo', 0, 8, 120.00, 'lendario', '["samurai"]', 'corpo', 'Armadura do lendário Musashi');

-- Armaduras Guerreiro
INSERT INTO equipments (nome, tipo, subtipo, atk_bonus, def_bonus, preco, raridade, classes_permitidas, slot_equipamento, descricao) VALUES
('Armadura Simples', 'armadura', 'corpo', 0, 6, 55.00, 'comum', '["guerreiro", "cavaleiro", "guardiao"]', 'corpo', 'Armadura básica de guerra'),
('Proteção de Peito', 'armadura', 'corpo', 0, 5, 45.00, 'comum', '["guerreiro", "cavaleiro", "guardiao", "andarilho"]', 'corpo', 'Peitoral protetor'),
('Braceletes e Caneleiras', 'armadura', 'membros', 0, 4, 45.00, 'comum', '["guerreiro", "cavaleiro", "guardiao", "monge"]', 'membros', 'Proteção para membros'),
('Capacete', 'armadura', 'cabeça', 0, 3, 30.00, 'comum', '["guerreiro", "cavaleiro", "guardiao"]', 'cabeça', 'Capacete de metal'),
('Armadura Completa', 'armadura', 'corpo', -2, 11, 115.00, 'epico', '["guerreiro", "cavaleiro", "guardiao"]', 'corpo', 'Armadura completa pesada');

-- Consumíveis e Equipamentos Gerais
INSERT INTO equipments (nome, tipo, subtipo, hp_bonus, efeito_especial, preco, raridade, consumivel, quantidade_uso, peso, classes_permitidas, descricao) VALUES
('Shurikens', 'consumivel', 'arremesso', 0, '+2 ATK em ataques à distância', 20.00, 'comum', TRUE, 20, 1, '["ninja"]', 'Estrelas ninja (20 unidades)'),
('Kunais', 'consumivel', 'arremesso', 0, '+3 ATK em ataques à distância', 22.00, 'comum', TRUE, 20, 1, '["ninja"]', 'Kunais ninja (20 unidades)'),
('Granada Explosiva', 'consumivel', 'explosivo', 0, '-15 HP no inimigo', 40.00, 'incomum', TRUE, 1, 1, '["ninja", "guerreiro", "andarilho"]', 'Granada de dano'),
('Granada de Flash', 'consumivel', 'explosivo', 0, '-18 ATK inimigo / +5 ATK próprio', 50.00, 'raro', TRUE, 1, 1, '["ninja"]', 'Granada cegante'),
('Granada de Fumaça', 'consumivel', 'explosivo', 0, '-9 ATK inimigo / -4 HP inimigo', 30.00, 'incomum', TRUE, 1, 1, '["ninja"]', 'Granada de fumaça'),
('Ataduras', 'consumivel', 'cura', 20, 'Recupera +20 HP', 50.00, 'comum', TRUE, 1, 1, NULL, 'Bandagens medicinais'),
('Medicamentos', 'consumivel', 'cura', 40, 'Recupera +40 HP', 60.00, 'incomum', TRUE, 1, 1, NULL, 'Remédios avançados'),
('Remédios', 'consumivel', 'cura', 30, 'Recupera +30 HP (1 rodada para efeito)', 55.00, 'incomum', TRUE, 1, 1, NULL, 'Poção curativa'),
('Tônico de Velocidade', 'consumivel', 'buff', 0, '+10 ATK / +5 DEF até o fim do combate', 95.00, 'raro', TRUE, 1, 1, '["samurai", "ninja", "andarilho"]', 'Aumenta velocidade'),
('Tônico de Saúde', 'consumivel', 'cura', 0, 'Cura penalidades por poções', 160.00, 'epico', TRUE, 1, 1, NULL, 'Remove efeitos negativos'),
('Bomba G', 'consumivel', 'explosivo', 0, '-30 HP inimigo / -15 HP próprio', 70.00, 'raro', TRUE, 1, 1, '["guerreiro", "andarilho"]', 'Bomba devastadora');

-- Ferramentas e Acessórios
INSERT INTO equipments (nome, tipo, subtipo, efeito_especial, preco, raridade, consumivel, quantidade_uso, peso, classes_permitidas, descricao) VALUES
('Afiador', 'ferramenta', 'manutenção', 'Restaura arma danificada', 15.00, 'comum', FALSE, 1, 1, NULL, 'Para amolar armas'),
('Reparador de Armaduras', 'ferramenta', 'manutenção', 'Repara armaduras (até 7 usos)', 30.00, 'comum', FALSE, 7, 1, '["guerreiro", "cavaleiro", "guardiao"]', 'Kit de reparo'),
('Martelo para Escudo', 'ferramenta', 'manutenção', 'Conserta escudos', 25.00, 'comum', FALSE, 1, 2, '["guerreiro", "cavaleiro", "guardiao"]', 'Martelo de reparo'),
('Bainha da Espada', 'acessorio', 'transporte', 'Protege a espada', 20.00, 'comum', FALSE, 1, 1, NULL, 'Bainha para espada'),
('Cinto para Espada', 'acessorio', 'transporte', 'Permite carregar espada', 15.00, 'comum', FALSE, 1, 1, NULL, 'Cinto de arma'),
('Acessórios', 'acessorio', 'transporte', 'Guarda armas extras', 10.00, 'comum', FALSE, 1, 1, NULL, 'Bolsas e suportes'),
('Cordas', 'ferramenta', 'utilitário', 'Para escalar e amarrar', 8.00, 'comum', FALSE, 1, 1, NULL, 'Cordas resistentes'),
('Correntes', 'ferramenta', 'utilitário', 'Para prender e imobilizar', 12.00, 'comum', FALSE, 1, 2, NULL, 'Correntes de metal'),
('Shinobi Kumade', 'ferramenta', 'utilitário', 'Corda com ganchos para escalar', 25.00, 'incomum', FALSE, 1, 2, '["ninja"]', 'Gancho ninja'),
('Fukumi Bari', 'consumivel', 'veneno', 'Dardos envenenados (-5 ATK inimigo)', 40.00, 'raro', TRUE, 5, 1, '["ninja"]', 'Dardos venenosos');

-- Roupas e Vestimentas
INSERT INTO equipments (nome, tipo, subtipo, def_bonus, efeito_especial, preco, raridade, slot_equipamento, classes_permitidas, descricao) VALUES
('Roupas Ninja Preta', 'acessorio', 'roupa', 0, 'Camuflagem noturna', 15.00, 'comum', 'corpo', '["ninja"]', 'Traje ninja preto'),
('Roupas Ninja Branca', 'acessorio', 'roupa', 0, 'Camuflagem em neve', 15.00, 'comum', 'corpo', '["ninja"]', 'Traje ninja branco'),
('Kimono Simples', 'acessorio', 'roupa', 0, NULL, 13.00, 'comum', 'corpo', '["ninja", "samurai", "monge"]', 'Kimono básico'),
('Kimono de Couro', 'acessorio', 'roupa', 1, 'Leve proteção', 25.00, 'comum', 'corpo', '["ninja", "samurai", "andarilho"]', 'Kimono reforçado'),
('Roupas de Tecido', 'acessorio', 'roupa', 0, NULL, 5.00, 'comum', 'corpo', NULL, 'Roupas comuns'),
('Roupas de Malha', 'acessorio', 'roupa', 1, NULL, 10.00, 'comum', 'corpo', '["guerreiro", "andarilho"]', 'Roupas de malha'),
('Roupas de Couro', 'acessorio', 'roupa', 1, NULL, 15.00, 'comum', 'corpo', NULL, 'Roupas de couro'),
('Casaco de Couro', 'acessorio', 'roupa', 1, NULL, 20.00, 'comum', 'corpo', NULL, 'Casaco protetor'),
('Botas', 'acessorio', 'calçado', 0, NULL, 10.00, 'comum', 'pés', NULL, 'Botas de viagem'),
('Sandália', 'acessorio', 'calçado', 0, NULL, 5.00, 'comum', 'pés', '["samurai", "monge"]', 'Sandálias tradicionais'),
('Luvas', 'acessorio', 'mãos', 0, NULL, 7.00, 'comum', 'mãos', NULL, 'Luvas simples'),
('Máscaras', 'acessorio', 'cabeça', 0, NULL, 7.00, 'comum', 'cabeça', NULL, 'Máscara facial'),
('Chapéu', 'acessorio', 'cabeça', 0, '+honra', 7.00, 'comum', 'cabeça', '["samurai", "guerreiro"]', 'Chapéu tradicional'),
('Capas', 'acessorio', 'costas', 0, '+respeito', 5.00, 'comum', 'costas', '["samurai", "cavaleiro"]', 'Capa nobre'),
('Faixa para Testa', 'acessorio', 'cabeça', 0, '+honra e respeito', 5.00, 'comum', 'cabeça', '["ninja", "samurai", "monge"]', 'Bandana'),
('Faixa para Cintura', 'acessorio', 'cintura', 0, '+reconhecimento', 5.00, 'comum', 'cintura', '["samurai", "monge"]', 'Obi tradicional');
