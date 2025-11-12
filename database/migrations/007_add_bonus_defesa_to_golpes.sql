-- Migration: Adicionar campo bonus_defesa na tabela golpes_templates
-- Data: 2025-11-12
-- Descrição: Adiciona campo para armazenar bônus de defesa que golpes do tipo 'defesa' concedem

-- Adicionar coluna bonus_defesa
ALTER TABLE golpes_templates
ADD COLUMN bonus_defesa INT DEFAULT 0 COMMENT 'Bônus de defesa concedido pelo golpe (principalmente para tipo defesa)' AFTER dano_extra;

-- Atualizar golpes existentes do tipo 'defesa' com valores de bonus_defesa
-- (Caso existam golpes de defesa, eles receberão valores baseados no nível)
UPDATE golpes_templates
SET bonus_defesa = CASE nivel_minimo
    WHEN 'principiante' THEN 10
    WHEN 'experiente' THEN 20
    WHEN 'veterano' THEN 35
    WHEN 'mestre' THEN 50
    ELSE 5
END
WHERE tipo = 'defesa';

-- Comentário explicativo
-- Para golpes de tipo 'defesa', o campo bonus_defesa indica quanto de defesa o golpe adiciona
-- Para outros tipos, pode ser 0 ou um valor secundário se o golpe também tiver efeito defensivo
