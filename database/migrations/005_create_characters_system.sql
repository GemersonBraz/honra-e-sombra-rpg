-- =====================================================
-- MIGRATION 003: Sistema de Personagens
-- Criado em: 11/11/2025
-- Descrição: Estrutura completa para criação e gerenciamento de personagens
-- =====================================================

-- Tabela principal de personagens
CREATE TABLE IF NOT EXISTS `characters` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `nome` VARCHAR(100) NOT NULL,
    `classe` ENUM('ninja', 'samurai', 'guerreiro', 'cavaleiro', 'cacador', 'assassino', 'guardiao', 'mago', 'monge') NOT NULL,
    `nivel` ENUM('principiante', 'experiente', 'veterano', 'mestre') DEFAULT 'principiante',
    `sexo` ENUM('masculino', 'feminino') NOT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    
    -- Atributos de Combate
    `hp_atual` INT DEFAULT 100,
    `hp_maximo` INT DEFAULT 100,
    `atk_base` INT DEFAULT 0,
    `def_base` INT DEFAULT 0,
    
    -- Elemental
    `elemento_principal` ENUM('fogo', 'agua', 'terra', 'ar', 'luz', 'trevas', 'raio', 'gelo', 'natureza') NOT NULL,
    `elemento_extra` ENUM('fogo', 'agua', 'terra', 'ar', 'luz', 'trevas', 'raio', 'gelo', 'natureza') DEFAULT NULL,
    
    -- Experiência e Progressão
    `xp` INT DEFAULT 0,
    `pontos_habilidade` INT DEFAULT 3, -- Pontos para gastar em habilidades/golpes
    
    -- Informações Narrativas
    `historia` TEXT DEFAULT NULL,
    `personalidade` VARCHAR(500) DEFAULT NULL,
    `objetivos` VARCHAR(500) DEFAULT NULL,
    
    -- Equipamentos Principais (IDs das tabelas de equipamentos)
    `arma_principal_id` INT UNSIGNED DEFAULT NULL,
    `arma_secundaria_id` INT UNSIGNED DEFAULT NULL,
    `armadura_id` INT UNSIGNED DEFAULT NULL,
    `escudo_id` INT UNSIGNED DEFAULT NULL,
    
    -- Recursos
    `dinheiro` INT DEFAULT 100,
    
    -- Status
    `status` ENUM('ativo', 'ferido', 'coma', 'morto') DEFAULT 'ativo',
    `ativo` TINYINT(1) DEFAULT 1,
    
    -- Metadata
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `data_atualizacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_classe` (`classe`),
    INDEX `idx_nivel` (`nivel`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Habilidades Secundárias do Personagem
-- =====================================================
CREATE TABLE IF NOT EXISTS `character_habilidades` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `character_id` INT UNSIGNED NOT NULL,
    `categoria` ENUM('magia', 'arma', 'fisico', 'mental') NOT NULL,
    `tipo` VARCHAR(50) NOT NULL, -- Ex: 'ambidestria', 'forca', 'telepatia', etc
    `nome` VARCHAR(100) NOT NULL,
    `descricao` TEXT DEFAULT NULL,
    `bonus_atk` INT DEFAULT 0,
    `bonus_def` INT DEFAULT 0,
    `bonus_hp` INT DEFAULT 0,
    `nivel_requerido` ENUM('principiante', 'experiente', 'veterano', 'mestre') DEFAULT 'principiante',
    `ativo` TINYINT(1) DEFAULT 1,
    `data_adquirida` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
    INDEX `idx_character_id` (`character_id`),
    INDEX `idx_categoria` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Golpes Especiais
-- =====================================================
CREATE TABLE IF NOT EXISTS `character_golpes_especiais` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `character_id` INT UNSIGNED NOT NULL,
    `nome` VARCHAR(100) NOT NULL,
    `tipo` ENUM('basico', 'por_rodadas', 'elemental', 'skill', 'misto') NOT NULL,
    `dano_base` INT NOT NULL, -- Base do nível (5, 10, 15, 20)
    `dano_extra` VARCHAR(20) DEFAULT NULL, -- Ex: '1d6', '2d6'
    `duracao_rodadas` VARCHAR(20) DEFAULT NULL, -- Ex: '1d6', '3'
    `elemento` ENUM('fogo', 'agua', 'terra', 'ar', 'luz', 'trevas', 'raio', 'gelo', 'natureza') DEFAULT NULL,
    `descricao` TEXT DEFAULT NULL,
    `usos_maximos` INT DEFAULT 1, -- Quantas vezes pode usar
    `usos_restantes` INT DEFAULT 1,
    `pontos_gastos` INT DEFAULT 1, -- Quantos pontos foram gastos para criar/melhorar
    `nivel_criacao` ENUM('principiante', 'experiente', 'veterano', 'mestre') NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
    INDEX `idx_character_id` (`character_id`),
    INDEX `idx_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Armas (Catálogo)
-- =====================================================
CREATE TABLE IF NOT EXISTS `armas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `classe_permitida` VARCHAR(100) DEFAULT NULL, -- Ex: 'ninja,samurai' ou NULL para todas
    `tipo` ENUM('leve', 'pesada', 'especial', 'dupla') NOT NULL,
    `atk_bonus` INT DEFAULT 0,
    `def_bonus` INT DEFAULT 0,
    `valor` INT DEFAULT 0,
    `descricao` TEXT DEFAULT NULL,
    `requer_curso` VARCHAR(50) DEFAULT NULL, -- Ex: 'duas_armas', 'arma_leve'
    `imagem` VARCHAR(255) DEFAULT NULL,
    `disponivel` TINYINT(1) DEFAULT 1,
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Armaduras (Catálogo)
-- =====================================================
CREATE TABLE IF NOT EXISTS `armaduras` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `classe_permitida` VARCHAR(100) DEFAULT NULL,
    `tipo` ENUM('leve', 'media', 'pesada') NOT NULL,
    `def_bonus` INT DEFAULT 0,
    `penalidade_atk` INT DEFAULT 0, -- Penalidade no ataque se ultrapassar proteção total
    `valor` INT DEFAULT 0,
    `descricao` TEXT DEFAULT NULL,
    `imagem` VARCHAR(255) DEFAULT NULL,
    `disponivel` TINYINT(1) DEFAULT 1,
    `data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Inventário do Personagem
-- =====================================================
CREATE TABLE IF NOT EXISTS `character_inventario` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `character_id` INT UNSIGNED NOT NULL,
    `tipo` ENUM('arma', 'armadura', 'escudo', 'consumivel', 'equipamento', 'quest') NOT NULL,
    `item_id` INT UNSIGNED DEFAULT NULL, -- ID na tabela correspondente (armas, armaduras, etc)
    `nome_item` VARCHAR(100) NOT NULL,
    `quantidade` INT DEFAULT 1,
    `equipado` TINYINT(1) DEFAULT 0,
    `descricao` TEXT DEFAULT NULL,
    `data_adquirido` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
    INDEX `idx_character_id` (`character_id`),
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_equipado` (`equipado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Penalidades Ativas (Buffs/Debuffs)
-- =====================================================
CREATE TABLE IF NOT EXISTS `character_penalidades` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `character_id` INT UNSIGNED NOT NULL,
    `tipo` ENUM('fome', 'sede', 'ferimento', 'envenenamento', 'buff', 'debuff', 'magia') NOT NULL,
    `nome` VARCHAR(100) NOT NULL,
    `modificador_hp` INT DEFAULT 0,
    `modificador_atk` INT DEFAULT 0,
    `modificador_def` INT DEFAULT 0,
    `duracao_rodadas` INT DEFAULT NULL, -- NULL = permanente
    `rodadas_restantes` INT DEFAULT NULL,
    `descricao` TEXT DEFAULT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `data_inicio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `data_expiracao` TIMESTAMP DEFAULT NULL,
    
    FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
    INDEX `idx_character_id` (`character_id`),
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Tabela de Histórico de Combates
-- =====================================================
CREATE TABLE IF NOT EXISTS `character_historico_combates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `character_id` INT UNSIGNED NOT NULL,
    `tipo_combate` ENUM('pvp', 'pve', 'treino', 'boss', 'evento') NOT NULL,
    `oponente` VARCHAR(100) DEFAULT NULL,
    `resultado` ENUM('vitoria', 'derrota', 'empate', 'fuga') NOT NULL,
    `xp_ganho` INT DEFAULT 0,
    `dinheiro_ganho` INT DEFAULT 0,
    `hp_perdido` INT DEFAULT 0,
    `descricao` TEXT DEFAULT NULL,
    `data_combate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
    INDEX `idx_character_id` (`character_id`),
    INDEX `idx_resultado` (`resultado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Inserir Armas Iniciais (Exemplos baseados no documento)
-- =====================================================
INSERT INTO `armas` (`nome`, `classe_permitida`, `tipo`, `atk_bonus`, `def_bonus`, `valor`, `requer_curso`) VALUES
-- Humano Comum
('Faca', NULL, 'leve', 2, 1, 8, NULL),
('Facão', NULL, 'leve', 3, 2, 12, NULL),
('Adaga', NULL, 'leve', 4, 2, 12, NULL),
('Adaga Dupla', NULL, 'dupla', 5, 2, 20, 'duas_armas'),
('Espada de Madeira', NULL, 'leve', 3, 3, 20, NULL),
('Bastão de Madeira', NULL, 'pesada', 3, 3, 23, NULL),
('Machadinha', NULL, 'pesada', 4, 1, 25, NULL),

-- Ninja
('Espada de Treino', 'ninja', 'leve', 2, 2, 13, NULL),
('Katana', 'ninja,samurai', 'leve', 5, 4, 40, NULL),
('Katana Dupla', 'ninja,samurai', 'dupla', 8, 6, 65, 'duas_armas'),
('Sabre', 'ninja,samurai', 'leve', 4, 5, 40, NULL),
('Espada Curta', 'ninja', 'leve', 4, 3, 35, NULL),
('Espada Curta Dupla', 'ninja', 'dupla', 6, 4, 55, 'duas_armas'),
('Espada Ninja', 'ninja', 'leve', 7, 5, 85, NULL),
('Espada Shinobi', 'ninja', 'pesada', 10, 8, 100, NULL),
('Bastão de Metal', 'ninja,monge', 'pesada', 6, 6, 60, NULL),
('Tridente Duplo', 'ninja', 'pesada', 9, 5, 80, NULL),
('Sai', 'ninja', 'leve', 4, 2, 40, NULL),
('Sai Duplo', 'ninja', 'dupla', 6, 3, 60, 'duas_armas'),
('Foice Curta', 'ninja', 'leve', 5, 3, 50, NULL),
('Foice com Corrente', 'ninja', 'especial', 7, 4, 60, NULL);

-- =====================================================
-- Inserir Armaduras Iniciais
-- =====================================================
INSERT INTO `armaduras` (`nome`, `classe_permitida`, `tipo`, `def_bonus`, `penalidade_atk`, `valor`) VALUES
('Roupas Comuns', NULL, 'leve', 0, 0, 20),
('Couro Leve', NULL, 'leve', 2, 0, 50),
('Couro Reforçado', NULL, 'media', 4, 1, 100),
('Cota de Malha', 'cavaleiro,guerreiro', 'media', 6, 2, 150),
('Armadura de Placas', 'cavaleiro,guerreiro', 'pesada', 10, 3, 300),
('Vestes Ninja', 'ninja,assassino', 'leve', 3, 0, 80),
('Kimono Reforçado', 'samurai,ninja', 'leve', 4, 0, 120),
('Armadura Samurai', 'samurai', 'media', 8, 1, 250),
('Vestes Místicas', 'mago,monge,guardiao', 'leve', 2, 0, 90);

-- =====================================================
-- FIM DA MIGRATION 003
-- =====================================================
