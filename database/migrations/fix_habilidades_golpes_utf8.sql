-- Fix encoding for habilidades and golpes
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- Limpar tabelas
TRUNCATE TABLE golpes_templates;
TRUNCATE TABLE habilidades_disponiveis;

SET FOREIGN_KEY_CHECKS=1;

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
('Código Bushido', 'mental', 'buff', 'Aumenta todos atributos quando HP está baixo.', 'mestre', 'samurai', 5, 5, 3);

-- Habilidades de Guerreiro
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Quebra-Escudo', 'tecnica_arma', 'combate', 'Ataque poderoso que destrói defesas.', 'principiante', 'guerreiro', 3, 0, 1),
('Resistência de Batalha', 'fisico', 'buff', 'Reduz dano recebido por 3 turnos.', 'experiente', 'guerreiro', 0, 6, 2),
('Berserker', 'fisico', 'buff', 'Sacrifica defesa por enorme aumento de ataque.', 'veterano', 'guerreiro', 12, -5, 2),
('Veterano de Guerra', 'mental', 'passiva', 'Aumenta todos atributos permanentemente.', 'mestre', 'guerreiro', 3, 3, 3);

-- Habilidades de Mago
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Bola de Fogo', 'magia', 'ofensiva', 'Lança esfera de fogo contra inimigos.', 'principiante', 'mago', 4, 0, 1),
('Barreira Arcana', 'magia', 'defesa', 'Cria escudo mágico protetor.', 'experiente', 'mago', 0, 7, 2),
('Meteoro', 'magia', 'ofensiva', 'Invoca meteoro devastador em área.', 'veterano', 'mago', 15, 0, 3),
('Domínio Elemental', 'magia', 'passiva', 'Controle total sobre um elemento.', 'mestre', 'mago', 8, 0, 3);

-- Habilidades de Monge
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Punho de Ki', 'fisico', 'combate', 'Canaliza energia espiritual em golpe.', 'principiante', 'monge', 4, 0, 1),
('Meditação de Batalha', 'mental', 'cura', 'Recupera HP e aumenta foco.', 'experiente', 'monge', 0, 0, 2),
('Passo Fantasma', 'fisico', 'movimento', 'Movimentação extremamente rápida.', 'veterano', 'monge', 0, 4, 2),
('Iluminação', 'mental', 'buff', 'Estado supremo de combate.', 'mestre', 'monge', 6, 6, 3);

-- Habilidades de Caçador
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Tiro Certeiro', 'tecnica_arma', 'distancia', 'Disparo preciso de longo alcance.', 'principiante', 'cacador', 5, 0, 1),
('Armadilha Oculta', 'mental', 'suporte', 'Prepara armadilha que prende inimigos.', 'experiente', 'cacador', 0, 0, 2),
('Flecha Explosiva', 'tecnica_arma', 'ofensiva', 'Flecha com ponta explosiva.', 'veterano', 'cacador', 10, 0, 2),
('Olho de Águia', 'mental', 'passiva', 'Precisão absoluta em ataques.', 'mestre', 'cacador', 8, 0, 3);

-- Habilidades de Assassino
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Golpe nas Sombras', 'tecnica_arma', 'furtividade', 'Ataque letal de surpresa.', 'principiante', 'assassino', 7, 0, 1),
('Veneno Mortal', 'mental', 'debuff', 'Aplica veneno que causa dano contínuo.', 'experiente', 'assassino', 0, 0, 2),
('Execução Silenciosa', 'tecnica_arma', 'letal', 'Elimina instantaneamente inimigo fraco.', 'veterano', 'assassino', 20, 0, 3),
('Mestre das Sombras', 'mental', 'passiva', 'Domínio completo de assassinatos.', 'mestre', 'assassino', 10, 0, 3);

-- Habilidades de Guardião
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Muralha Viva', 'fisico', 'defesa', 'Torna-se barreira intransponível.', 'principiante', 'guardiao', 0, 10, 1),
('Provocação', 'mental', 'controle', 'Força inimigos a atacarem você.', 'experiente', 'guardiao', 0, 5, 2),
('Bastião Inabalável', 'fisico', 'defesa', 'Imune a knockback e atordoamento.', 'veterano', 'guardiao', 0, 12, 2),
('Guardião Eterno', 'mental', 'passiva', 'Resistência suprema.', 'mestre', 'guardiao', 0, 15, 3);

-- Habilidades de Cavaleiro
INSERT INTO habilidades_disponiveis (nome, categoria, tipo, descricao, nivel_minimo, classes_permitidas, bonus_atk, bonus_def, custo_pontos) VALUES
('Carga da Cavalaria', 'tecnica_arma', 'movimento', 'Ataque em alta velocidade montado.', 'principiante', 'cavaleiro', 6, 0, 1),
('Escudo Sagrado', 'magia', 'defesa', 'Proteção divina para aliados.', 'experiente', 'cavaleiro', 0, 8, 2),
('Lança do Destino', 'tecnica_arma', 'ofensiva', 'Golpe de lança devastador.', 'veterano', 'cavaleiro', 12, 0, 2),
('Nobre Guerreiro', 'mental', 'passiva', 'Inspiração para aliados.', 'mestre', 'cavaleiro', 4, 4, 3);

-- ============================================
-- INSERIR DADOS INICIAIS - GOLPES TEMPLATES
-- ============================================

-- Golpes Elementais
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, elemento, nivel_minimo, habilidade_requerida_id, custo_pontos) VALUES
('Katon: Bola de Fogo', 'magia_ofensiva', 'ninjutsu_elemental', 'Lança uma esfera de chamas contra o inimigo.', 15, 5, 'fogo', 'principiante', 1, 1),
('Katon: Grande Bola de Fogo', 'magia_ofensiva', 'ninjutsu_elemental', 'Versão amplificada da Bola de Fogo.', 25, 10, 'fogo', 'experiente', 1, 2),
('Katon: Dragão de Fogo', 'magia_ofensiva', 'ninjutsu_elemental', 'Invoca dragão flamejante massivo.', 40, 20, 'fogo', 'mestre', 1, 3),
('Suiton: Dragão Aquático', 'magia_ofensiva', 'ninjutsu_elemental', 'Cria dragão de água que ataca inimigos.', 35, 15, 'agua', 'veterano', 2, 3),
('Suiton: Onda Gigante', 'magia_ofensiva', 'ninjutsu_elemental', 'Gera onda devastadora.', 25, 10, 'agua', 'experiente', 2, 2),
('Doton: Parede de Terra', 'defesa', 'ninjutsu_elemental', 'Ergue barreira de pedra protetora.', 0, 0, 'terra', 'principiante', 3, 1),
('Doton: Meteoro Terrestre', 'magia_ofensiva', 'ninjutsu_elemental', 'Lança enormes rochas contra inimigos.', 35, 15, 'terra', 'veterano', 3, 3),
('Doton: Mão de Terra', 'suporte', 'ninjutsu_elemental', 'Cria mão de pedra que prende o alvo.', 10, 5, 'terra', 'experiente', 3, 2),
('Doton: Prisão de Pedra', 'suporte', 'ninjutsu_elemental', 'Aprisiona inimigo em rocha.', 15, 5, 'terra', 'experiente', 3, 2),
('Raiton: Mil Pássaros', 'magia_ofensiva', 'ninjutsu_elemental', 'Golpe elétrico mortal de alta velocidade.', 45, 20, 'raio', 'mestre', 4, 3),
('Raiton: Espada Relâmpago', 'magia_ofensiva', 'ninjutsu_elemental', 'Canaliza raios em arma.', 30, 15, 'raio', 'veterano', 4, 2),
('Raiton: Tempestade Elétrica', 'magia_ofensiva', 'ninjutsu_elemental', 'Invoca tempestade de raios.', 25, 12, 'raio', 'experiente', 4, 2),
('Fuuton: Lâmina de Vento', 'magia_ofensiva', 'ninjutsu_elemental', 'Corta inimigos com vento afiado.', 25, 10, 'ar', 'experiente', 5, 2),
('Fuuton: Devastação', 'magia_ofensiva', 'ninjutsu_elemental', 'Tornado massivo que destrói tudo.', 45, 20, 'ar', 'mestre', 5, 3),
('Fuuton: Voo Rápido', 'suporte', 'ninjutsu_elemental', 'Impulsiona movimento com vento.', 0, 0, 'ar', 'principiante', 5, 1),
('Hyouton: Espinhos de Gelo', 'magia_ofensiva', 'ninjutsu_elemental', 'Cria lanças de gelo afiadas.', 25, 10, 'gelo', 'experiente', 6, 2),
('Hyouton: Prisão Congelante', 'suporte', 'ninjutsu_elemental', 'Congela inimigo completamente.', 15, 8, 'gelo', 'veterano', 6, 2);

-- Golpes Físicos
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, elemento, nivel_minimo, habilidade_requerida_id, custo_pontos) VALUES
('Investida Poderosa', 'ataque_fisico', 'fisico', 'Ataque corpo a corpo devastador.', 20, 8, NULL, 'principiante', 9, 1),
('Golpe Giratório', 'ataque_fisico', 'fisico', 'Ataca todos ao redor em movimento circular.', 22, 10, NULL, 'experiente', 9, 2),
('Impacto Sísmico', 'ataque_fisico', 'fisico', 'Golpe tão forte que treme o chão.', 35, 15, 'terra', 'veterano', 9, 3),
('Corte Ascendente', 'ataque_fisico', 'tecnica_espada', 'Corte vertical devastador.', 18, 7, NULL, 'principiante', 5, 1),
('Dança das Lâminas', 'ataque_fisico', 'tecnica_espada', 'Sequência rápida de cortes.', 28, 12, NULL, 'experiente', 5, 2),
('Guilhotina', 'ataque_fisico', 'tecnica_espada', 'Corte mortal de cima para baixo.', 40, 18, NULL, 'mestre', 5, 3);

-- Golpes de Suporte
INSERT INTO golpes_templates (nome, tipo, categoria, descricao, dano_base, dano_extra, elemento, nivel_minimo, habilidade_requerida_id, custo_pontos) VALUES
('Cura Mística', 'cura', 'suporte', 'Restaura HP de aliados.', 0, 25, 'luz', 'principiante', 12, 1),
('Benção Divina', 'suporte', 'buff', 'Aumenta todos atributos de aliados.', 0, 0, 'luz', 'experiente', 12, 2),
('Ressurreição', 'cura', 'suporte', 'Revive aliado caído.', 0, 50, 'luz', 'mestre', 12, 3),
('Maldição das Trevas', 'suporte', 'debuff', 'Reduz atributos de inimigos.', 0, 0, 'trevas', 'experiente', 27, 2),
('Dreno de Vida', 'suporte', 'debuff', 'Absorve HP do inimigo.', 15, 8, 'trevas', 'veterano', 27, 2),
('Escudo Arcano', 'defesa', 'suporte', 'Cria barreira mágica protetora.', 0, 0, NULL, 'experiente', 14, 2),
('Velocidade Máxima', 'suporte', 'buff', 'Aumenta drasticamente velocidade.', 0, 0, 'ar', 'veterano', 31, 2);
