# Referência de Ícones (Honra e Sombra)

Guia rápido de ícones SVG da coleção `public/img/icons-1x1/` para padronizar ações e componentes nas próximas páginas.

## Como usar
- Caminho base: `/public/img/icons-1x1/<autor>/<arquivo>.svg`
- Exemplo PHP: `<img src="<?= SITE_URL ?>/public/img/icons-1x1/lorc/arrow-dunk.svg" alt="Voltar" class="w-4 h-4">`
- Exemplo em JS/HTML: `<img src="img/icons-1x1/lorc/arrow-dunk.svg" alt="Voltar">`

## Ícones atualmente em uso (amostra consolidada)
- lorc/arrow-dunk.svg: botões Voltar (admin) e navegação
- lorc/bordered-shield.svg: Registrar, Admin, badges
- lorc/castle.svg: Início/Home (navbar)
- lorc/checked-shield.svg: Salvar/confirmar (admin usuários)
- lorc/crossed-swords.svg: Entrar/combate/ações
- lorc/crowned-skull.svg: Usuários/seção admin
- lorc/dragon-head.svg: Marca e destaques
- lorc/eye-shield.svg: Ver/visualizar
- lorc/fire-ring.svg: Aviso/toasts
- lorc/flat-star.svg: Destaque/novo
- lorc/gears.svg: Configurações
- lorc/gems.svg: Conteúdo
- lorc/hammer-nails.svg: Manutenção
- lorc/key.svg: Senha/configurar
- lorc/moon.svg e lorc/sun.svg: Tema escuro/claro
- lorc/ninja-mask.svg: Tema "Sombra"/seções
- lorc/open-book.svg: Dashboard/Docs
- lorc/papers.svg: Logs
- lorc/quill-ink.svg: Editar
- lorc/scroll-unfurled.svg: Lista/Informações/Docs
- lorc/sliced-bread.svg: Toast (demo)
- lorc/skull-crossed-bones.svg: Erro/Deletar/alerta destrutivo
- lorc/sword-clash.svg: Golpes/ação
- lorc/trophy.svg: Sucesso/toasts
- lorc/wooden-door.svg: Sair/Logout/Voltar em páginas específicas

> Observação: lista gerada a partir de ocorrências no código (navbar, home, admin, toasts e páginas de conteúdo). Não é exaustiva, mas cobre os usos principais atuais.

## Padrões recomendados por ação
- Voltar: `lorc/arrow-dunk.svg` (em uso)
- Fechar (modal/caixas): `lorc/cross-mark.svg` (novo recomendado)
- Cancelar: `lorc/cross-mark.svg` (novo recomendado)
- Salvar/Confirmar: `lorc/checked-shield.svg` (em uso)
- Editar: `lorc/quill-ink.svg` (em uso)
- Deletar/Remover: `lorc/skull-crossed-bones.svg` (em uso)
- Criar/Adicionar/Novo: `lorc/flat-star.svg` (em uso em destaques) ou `lorc/tied-scroll.svg` (alternativa temática)
- Visualizar/Ver: `lorc/eye-shield.svg` (em uso)
- Informações/Detalhes: `lorc/scroll-unfurled.svg` (em uso)
- Entrar/Login: `lorc/crossed-swords.svg` (em uso)
- Sair/Logout: `lorc/wooden-door.svg` (em uso)
- Início/Home: `lorc/castle.svg` (em uso)
- Configurações: `lorc/gears.svg` (em uso)
- Tema Claro: `lorc/sun.svg` (em uso)
- Tema Escuro: `lorc/moon.svg` (em uso)
- Sucesso/OK: `lorc/trophy.svg` (em uso em toasts)
- Aviso/Warning: `lorc/fire-ring.svg` (em uso em toasts)
- Erro/Falha: `lorc/skull-crossed-bones.svg` (em uso em toasts)
- Pesquisa/Buscar: `lorc/spyglass.svg` (novo recomendado)
- Próximo/Avançar: `lorc/winged-arrow.svg` ou `lorc/wide-arrow-dunk.svg` (novos recomendados)
- Anterior/Voltar listagem: `lorc/return-arrow.svg` (novo recomendado)

### Cards de Golpes (substituição de emojis)
- Dano: `lorc/crossed-swords.svg`
- Defesa: `lorc/shield-echoes.svg`
- Elemento: `lorc/crystal-ball.svg`
- Nível: `lorc/medal.svg` (ou `lorc/laurel-crown.svg` alternativa)
- Usos Máx.: `lorc/cycle.svg`
- Duração: `lorc/stopwatch.svg` (ou `lorc/hourglass.svg` alternativa)
- Custo: `lorc/crown-coin.svg` (ou `lorc/cash.svg` alternativa)
- Classes: `lorc/archery-target.svg`
- Habilidade: `lorc/open-book.svg` (ou `lorc/scroll-unfurled.svg` alternativa)
- Efeitos: `lorc/spark-spirit.svg`

## Sugestões de consistência
- Preferir a coleção `lorc/` para ícones de UI (maior diversidade e já presente no projeto).
- Para ações destrutivas (deletar), manter `skull-crossed-bones.svg` + cores de perigo.
- Para confirmação/sucesso, manter `checked-shield.svg` ou `trophy.svg` conforme contexto (ação vs. feedback).
- Em botões com texto, manter tamanho padrão `w-4 h-4` (navbar) ou `w-5 h-5` (cards/modais) para consistência.

## Atalhos úteis (caminhos)
- Voltar: `/public/img/icons-1x1/lorc/arrow-dunk.svg`
- Fechar/Cancelar: `/public/img/icons-1x1/lorc/cross-mark.svg`
- Salvar: `/public/img/icons-1x1/lorc/checked-shield.svg`
- Editar: `/public/img/icons-1x1/lorc/quill-ink.svg`
- Deletar: `/public/img/icons-1x1/lorc/skull-crossed-bones.svg`
- Ver: `/public/img/icons-1x1/lorc/eye-shield.svg`
- Info: `/public/img/icons-1x1/lorc/scroll-unfurled.svg`
- Home: `/public/img/icons-1x1/lorc/castle.svg`
- Configurações: `/public/img/icons-1x1/lorc/gears.svg`
- Tema claro/escuro: `/public/img/icons-1x1/lorc/sun.svg` / `/public/img/icons-1x1/lorc/moon.svg`
- Pesquisa: `/public/img/icons-1x1/lorc/spyglass.svg`
- Próximo/Anterior: `/public/img/icons-1x1/lorc/winged-arrow.svg` / `/public/img/icons-1x1/lorc/return-arrow.svg`

---
Se quiser, posso gerar automaticamente uma versão desta lista com todos os arquivos de ícones varridos do projeto e marcar quais já estão em uso vs. ainda não usados.
