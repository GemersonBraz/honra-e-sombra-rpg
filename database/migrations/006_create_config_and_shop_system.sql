-- =====================================================
-- MIGRATION 004: Sistema de Configurações e Loja
-- Criado em: 11/11/2025
-- Descrição: Configurações globais controláveis pelo admin e sistema de loja
-- =====================================================

-- =====================================================
-- Tabela de Configurações do Sistema (Admin controla)
-- =====================================================
CREATE TABLE IF NOT EXISTS `game_config` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `chave` VARCHAR(100) NOT NULL UNIQUE,
    `valor` TEXT NOT NULL,
    `tipo` ENUM('inteiro', 'decimal', 'texto', 'booleano', 'json') DEFAULT 'texto',
    `categoria` VARCHAR(50) DEFAULT 'geral',
    `descricao` TEXT DEFAULT NULL,
    `editavel_admin` TINYINT(1) DEFAULT 1,
    `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_chave` (`chave`),
    INDEX `idx_categoria` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Inserir Configurações Padrão do Jogo
-- =====================================================
INSERT INTO `game_config` (`chave`, `valor`, `tipo`, `categoria`, `descricao`) VALUES
-- Configurações de Criação de Personagem
('character_pontos_iniciais', '3', 'inteiro', 'personagem', 'Pontos de habilidade que o jogador recebe ao criar personagem'),
('character_dinheiro_inicial', '100', 'inteiro', 'personagem', 'Quantidade de dinheiro inicial ao criar personagem'),
('character_hp_inicial', '100', 'inteiro', 'personagem', 'HP inicial de todos os personagens'),
('character_max_habilidades_iniciais', '2', 'inteiro', 'personagem', 'Máximo de habilidades secundárias que pode escolher na criação'),
('character_max_golpes_iniciais', '1', 'inteiro', 'personagem', 'Máximo de golpes especiais que pode criar na criação'),

-- Limites de Personagem
('character_max_por_jogador', '5', 'inteiro', 'personagem', 'Máximo de personagens que um jogador pode criar'),
('character_max_armas', '4', 'inteiro', 'personagem', 'Máximo de armas diferentes que pode carregar'),
('character_max_itens_pequenos', '9', 'inteiro', 'personagem', 'Máximo de itens pequenos no inventário'),

-- Sistema de Experiência
('xp_por_nivel_principiante', '0', 'inteiro', 'experiencia', 'XP necessário para começar como principiante'),
('xp_por_nivel_experiente', '1000', 'inteiro', 'experiencia', 'XP necessário para virar experiente'),
('xp_por_nivel_veterano', '3000', 'inteiro', 'experiencia', 'XP necessário para virar veterano'),
('xp_por_nivel_mestre', '6000', 'inteiro', 'experiencia', 'XP necessário para virar mestre'),
('pontos_habilidade_por_nivel', '2', 'inteiro', 'experiencia', 'Pontos de habilidade ganhos ao subir de nível'),

-- Sistema de Combate
('hp_recuperacao_por_dia', '10', 'inteiro', 'combate', 'HP recuperado por dia de descanso'),
('penalidade_hp_20', '-5', 'inteiro', 'combate', 'Penalidade de ATK quando HP <= 20'),
('penalidade_hp_5', '-10', 'inteiro', 'combate', 'Penalidade de ATK quando HP <= 5'),

-- Loja
('loja_taxa_venda', '0.5', 'decimal', 'loja', 'Multiplicador ao vender itens (50% do valor)'),
('loja_itens_aleatorios', '1', 'booleano', 'loja', 'Gerar itens aleatórios na loja'),
('loja_refresh_dias', '7', 'inteiro', 'loja', 'Dias para refresh de itens especiais');

-- =====================================================
-- Tabela de Itens da Loja (Catálogo Geral)
-- =====================================================
CREATE TABLE IF NOT EXISTS `loja_itens` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `tipo` ENUM('arma', 'armadura', 'escudo', 'consumivel', 'equipamento', 'especial') NOT NULL,
    `subtipo` VARCHAR(50) DEFAULT NULL, -- Ex: 'pocao_hp', 'mochila', 'cantil'
    `descricao` TEXT DEFAULT NULL,
    `preco_compra` INT NOT NULL,
    `preco_venda` INT NOT NULL, -- Calculado automaticamente ou manual
    
    -- Atributos (para armas/armaduras)
    `atk_bonus` INT DEFAULT 0,
    `def_bonus` INT DEFAULT 0,
    `hp_bonus` INT DEFAULT 0,
    
    -- Classe/Nível Requerido
    `classe_permitida` VARCHAR(100) DEFAULT NULL, -- NULL = todas, ou 'ninja,samurai'
    `nivel_minimo` ENUM('principiante', 'experiente', 'veterano', 'mestre') DEFAULT 'principiante',
    `requer_habilidade` VARCHAR(100) DEFAULT NULL, -- Ex: 'duas_armas', 'arma_pesada'
    
    -- Estoque e Disponibilidade
    `estoque_infinito` TINYINT(1) DEFAULT 1, -- Sempre disponível
    `estoque_atual` INT DEFAULT NULL, -- NULL = infinito
    `raridade` ENUM('comum', 'incomum', 'raro', 'epico', 'lendario') DEFAULT 'comum',
    
    -- Efeitos Especiais (JSON)
    `efeitos` JSON DEFAULT NULL, -- Ex: {"recupera_hp": 30, "duracao": "instantaneo"}
    
    -- Metadata
    `imagem` VARCHAR(255) DEFAULT NULL,
    `disponivel` TINYINT(1) DEFAULT 1, -- Admin pode desabilitar item
    `criado_por_admin` TINYINT(1) DEFAULT 0,
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_raridade` (`raridade`),
    INDEX `idx_disponivel` (`disponivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Histórico de Compras
-- =====================================================
CREATE TABLE IF NOT EXISTS `loja_transacoes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `character_id` INT UNSIGNED NOT NULL,
    `item_id` INT UNSIGNED NOT NULL,
    `tipo_transacao` ENUM('compra', 'venda') NOT NULL,
    `quantidade` INT DEFAULT 1,
    `preco_unitario` INT NOT NULL,
    `preco_total` INT NOT NULL,
    `dinheiro_antes` INT NOT NULL,
    `dinheiro_depois` INT NOT NULL,
    `data_transacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`item_id`) REFERENCES `loja_itens`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_character_id` (`character_id`),
    INDEX `idx_tipo` (`tipo_transacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Inserir Itens Básicos na Loja
-- =====================================================
INSERT INTO `loja_itens` (`nome`, `tipo`, `subtipo`, `descricao`, `preco_compra`, `preco_venda`, `atk_bonus`, `def_bonus`, `raridade`) VALUES
-- Armas Básicas (Comum)
('Faca Simples', 'arma', 'leve', 'Uma faca básica para iniciantes', 8, 4, 2, 1, 'comum'),
('Facão', 'arma', 'leve', 'Facão resistente para cortes', 12, 6, 3, 2, 'comum'),
('Adaga', 'arma', 'leve', 'Adaga afiada e leve', 12, 6, 4, 2, 'comum'),
('Espada de Madeira', 'arma', 'leve', 'Espada de treino em madeira', 20, 10, 3, 3, 'comum'),
('Bastão de Madeira', 'arma', 'pesada', 'Bastão robusto de madeira', 23, 11, 3, 3, 'comum'),

-- Armaduras Básicas
('Roupas Comuns', 'armadura', 'leve', 'Roupas simples do dia a dia', 20, 10, 0, 0, 'comum'),
('Couro Leve', 'armadura', 'leve', 'Armadura leve de couro', 50, 25, 0, 2, 'comum'),
('Couro Reforçado', 'armadura', 'media', 'Armadura de couro reforçado com metal', 100, 50, 0, 4, 'incomum'),

-- Consumíveis
('Poção de Vida Pequena', 'consumivel', 'pocao_hp', 'Recupera 20 HP', 30, 15, 0, 0, 'comum'),
('Poção de Vida Média', 'consumivel', 'pocao_hp', 'Recupera 50 HP', 80, 40, 0, 0, 'incomum'),
('Poção de Vida Grande', 'consumivel', 'pocao_hp', 'Recupera 100 HP', 150, 75, 0, 0, 'raro'),
('Antídoto', 'consumivel', 'antidoto', 'Remove envenenamento', 50, 25, 0, 0, 'comum'),
('Comida', 'consumivel', 'comida', 'Recupera 15 HP e remove fome', 30, 10, 0, 0, 'comum'),
('Água', 'consumivel', 'agua', 'Remove sede e recupera 10 HP', 10, 3, 0, 0, 'comum'),

-- Equipamentos
('Mochila Simples', 'equipamento', 'mochila', 'Capacidade: 10 itens', 30, 15, 0, 0, 'comum'),
('Mochila Média', 'equipamento', 'mochila', 'Capacidade: 20 itens', 40, 20, 0, 0, 'comum'),
('Mochila Grande', 'equipamento', 'mochila', 'Capacidade: 40 itens', 60, 30, 0, 0, 'incomum'),
('Cantil Simples', 'equipamento', 'cantil', 'Armazena 2L de água', 15, 7, 0, 0, 'comum'),
('Cantil Regular', 'equipamento', 'cantil', 'Armazena 6L de água', 35, 17, 0, 0, 'comum'),
('Cantil Profissional', 'equipamento', 'cantil', 'Armazena 15L de água', 55, 27, 0, 0, 'incomum'),
('Kit de Primeiros Socorros', 'equipamento', 'medicamento', 'Recupera 30 HP', 50, 25, 0, 0, 'comum');

-- Armas Especiais Ninja (Raras)
INSERT INTO `loja_itens` (`nome`, `tipo`, `subtipo`, `descricao`, `preco_compra`, `preco_venda`, `atk_bonus`, `def_bonus`, `classe_permitida`, `raridade`, `requer_habilidade`) VALUES
('Katana', 'arma', 'leve', 'Espada samurai tradicional', 40, 20, 5, 4, 'ninja,samurai', 'incomum', NULL),
('Katana Dupla', 'arma', 'dupla', 'Par de katanas para combate dual', 65, 32, 8, 6, 'ninja,samurai', 'raro', 'duas_armas'),
('Espada Ninja', 'arma', 'leve', 'Espada ninja lendária', 85, 42, 7, 5, 'ninja', 'raro', NULL),
('Espada Shinobi', 'arma', 'pesada', 'A arma definitiva dos shinobis', 100, 50, 10, 8, 'ninja', 'epico', NULL),
('Sai Duplo', 'arma', 'dupla', 'Armas de defesa ninja', 60, 30, 6, 3, 'ninja', 'raro', 'duas_armas');

-- =====================================================
-- View para Loja (Itens Disponíveis)
-- =====================================================
CREATE OR REPLACE VIEW `vw_loja_disponivel` AS
SELECT 
    li.*,
    CASE 
        WHEN li.estoque_infinito = 1 THEN 'Ilimitado'
        WHEN li.estoque_atual IS NULL THEN 'Ilimitado'
        ELSE CAST(li.estoque_atual AS CHAR)
    END AS estoque_display
FROM `loja_itens` li
WHERE li.disponivel = 1
  AND (li.estoque_infinito = 1 OR li.estoque_atual > 0 OR li.estoque_atual IS NULL);

-- =====================================================
-- FIM DA MIGRATION 004
-- =====================================================
