# 🗡️ Honra e Sombra RPG# Sistema Honra e Sombra RPG - Parte 1



Um sistema completo de RPG oriental com interface moderna, sistema de toasts interativos e tema personalizado.## 🎯 Visão Geral



## 🎮 FuncionalidadesSistema web modular para o RPG de mesa "Honra e Sombra", desenvolvido em PHP 7.2 com MySQL e Tailwind CSS.



### 🔐 Sistema de Autenticação## 📁 Estrutura de Arquivos

- Login e registro de usuários

- Sessões seguras```

- Controle de acesso admin/playerHonra-e-Sombra/

├── config/

### 🎨 Interface│   └── db.php                 # Configuração do banco de dados

- **Tema Oriental Customizado**: Design inspirado em cultura japonesa/asiática├── app/

- **Sistema de Toasts Interativo**: Notificações com ícones SVG e animações│   └── models/

- **Layout Responsivo**: Funciona em desktop, tablet e mobile│       └── User.php           # Model do usuário

- **Ícones Temáticos**: Coleção Lorc com mais de 1000 ícones SVG├── views/

│   ├── layout.php             # Template base

### ⚔️ Características RPG│   ├── home.php               # Página inicial

- Sistema de classes orientais (Samurai, Ninja, Monge, etc.)│   ├── register.php           # Registro de usuário

- Elementos místicos (Fogo, Água, Terra, Ar, Metal)│   ├── login.php              # Login

- Sistema de personagens (em desenvolvimento)│   └── dashboard.php          # Dashboard do usuário

- Missões e conquistas (em desenvolvimento)├── public/

│   └── index.php              # Arquivo principal (router)

### 🔧 Sistema Técnico├── database/

- **Backend**: PHP 8+ com PDO│   └── schema.sql             # Schema do banco de dados

- **Frontend**: JavaScript vanilla, Tailwind CSS├── .htaccess                  # Configuração Apache

- **Database**: MySQL└── README.md                  # Este arquivo

- **Servidor**: Apache (XAMPP)```



## 📋 Requisitos## 🚀 Como Instalar/Rodar (XAMPP)



- PHP 8.0 ou superior### 1. Preparar o Ambiente

- MySQL 5.7 ou superior1. Instale o **XAMPP** (https://www.apachefriends.org/)

- Apache 2.4 ou superior2. Inicie o **Apache** e **MySQL** no painel do XAMPP

- Extensões PHP: PDO, PDO_MySQL3. Clone ou extraia os arquivos na pasta `c:\xampp\htdocs\Honra-e-Sombra`



## 🚀 Instalação### 2. Configurar o Banco de Dados

1. Abra o **phpMyAdmin** em `http://localhost/phpmyadmin`

### 1. Clone o repositório2. Execute o arquivo `database/schema.sql` para criar o banco

```bash3. Verifique se a tabela `users` foi criada com o usuário admin

git clone https://github.com/seu-usuario/honra-e-sombra-rpg.git

cd honra-e-sombra-rpg### 3. Configurar Permissões

```- Certifique-se que o módulo **mod_rewrite** está ativo no Apache

- Se necessário, ajuste as configurações em `config/db.php`

### 2. Configure o banco de dados

```bash### 4. Acessar o Sistema

# Crie um banco de dados MySQL- URL principal: `http://localhost/Honra-e-Sombra`

# Importe o schema do arquivo: database/schema.sql- Login admin: `admin@honrasombra.com` / `admin123`

```

## 🔧 Funcionalidades Implementadas (Parte 1)

### 3. Configure a conexão

```php### ✅ Autenticação Básica

// Edite config/db.php com suas credenciais- [x] Registro de usuários

$host = 'localhost';- [x] Login/logout

$dbname = 'honra_sombra_rpg';- [x] Sessões seguras

$username = 'seu_usuario';- [x] Validação de dados

$password = 'sua_senha';- [x] Hash de senhas

```

### ✅ Interface

### 4. Configure o servidor web- [x] Layout responsivo com Tailwind CSS

- Aponte o DocumentRoot para a pasta `public/`- [x] Tema Honra (claro) e Sombra (escuro)

- Ou acesse via `http://localhost/honra-e-sombra-rpg/public/`- [x] Navegação intuitiva

- [x] Mensagens de feedback

## 🎯 Demonstrações- [x] Página 404 personalizada



### Sistema de Toasts### ✅ Estrutura Base

Acesse: `/public/index.php?page=toast-demo`- [x] Router simples mas eficaz

- [x] Model-View-Controller básico

Funcionalidades demonstradas:- [x] Sistema modular preparado para crescer

- ✅ Toasts básicos (sucesso, erro, aviso, info)- [x] Configuração de segurança básica

- ✅ Ícones SVG coloridos e temáticos

- ✅ Animações de entrada e saída## 🎮 Contas de Teste

- ✅ Barra de progresso funcional

- ✅ Botão de fechar### Administrador

- ✅ Durações customizáveis- **Email:** admin@honrasombra.com

- ✅ Toasts de autenticação, RPG e sistema- **Senha:** admin123

- **Funcionalidades:** Acesso completo (quando implementado)

## 🔔 Sistema de Toasts

### Novo Usuário

### Uso Básico- Crie uma conta através da página de registro

```javascript- Acesse funcionalidades de jogador

// Toasts básicos

toast.success('Operação realizada!');## 🔐 Segurança Implementada

toast.error('Algo deu errado!');

toast.warning('Atenção necessária!');- Hash de senhas com `password_hash()`

toast.info('Informação importante!');- Sanitização de inputs

- Proteção contra SQL Injection (PDO)

// Com opções avançadas- Sessões seguras

toast.success('Mensagem', {- Headers de segurança básicos

    title: 'Título Personalizado',

    duration: 5000,## 📋 Próximos Passos

    action: {

        text: 'Ação',### PARTE 2 - Área do Jogador + Personagens

        handler: 'alert("Clicou!")'- Tabela `characters`

    }- CRUD de personagens

});- Listagem de personagens do usuário

```- Formulário de criação/edição



### Toasts Especializados### PARTE 3 - Classes, Elementos e Habilidades

```javascript- Tabelas: `classes`, `elements`, `secondary_skills`

// Autenticação- Páginas públicas de consulta

authToasts.loginSuccess('Nome do Usuário');- Sistema de associação personagem ↔ habilidades

authToasts.loginError();

authToasts.registerSuccess();## 🐛 Resolução de Problemas

authToasts.logoutSuccess();

### Erro "Page not found" em todas as rotas

// RPG- Verifique se o **mod_rewrite** está ativo no Apache

rpgToasts.levelUp(15);- Confirme se o arquivo `.htaccess` está na pasta raiz

rpgToasts.questCompleted('Nome da Quest');

rpgToasts.itemObtained('Espada Lendária');### Erro de conexão com banco

rpgToasts.characterCreated('Akira', 'Ninja');- Verifique se o MySQL está rodando no XAMPP

- Confirme as configurações em `config/db.php`

// Sistema- Execute o arquivo `database/schema.sql`

systemToasts.maintenance();

systemToasts.backupComplete();### Páginas sem estilo

systemToasts.featureComingSoon('Nova Feature');- Verifique se o Tailwind CSS está carregando

```- Confirme se não há erros de JavaScript no console



## 📁 Estrutura do Projeto## 💡 Estrutura Preparada Para



```- Sistema de permissões granular

honra-e-sombra-rpg/- Múltiplas classes de personagem

├── app/- Sistema de elementos e magias

│   ├── components/         # Componentes reutilizáveis- Bestiário colaborativo

│   ├── includes/          # Header, footer, navbar- Painel administrativo completo

│   ├── models/            # Models (User, etc.)- Sistema de contribuições de usuários

│   └── views/             # Views das páginas

├── config/## 📞 Suporte

│   └── db.php             # Configuração do banco

├── database/Este é um sistema modular em desenvolvimento. A Parte 1 estabelece a base sólida para todas as funcionalidades futuras do sistema Honra e Sombra RPG.
│   └── schema.sql         # Schema do banco de dados
├── public/
│   ├── css/               # Estilos e temas
│   ├── img/               # Imagens e ícones
│   ├── js/                # JavaScript (toasts, temas)
│   └── index.php          # Ponto de entrada
└── README.md
```

## 🎨 Sistema de Temas

O projeto inclui um sistema de temas completo:

### Temas Disponíveis
- **Honra** (Padrão): Tons dourados e vermelhos
- **Sombra**: Tons escuros e misteriosos
- **Natureza**: Verde e terroso
- **Gelo**: Azul e branco

## 🎮 Roadmap

### Fase 1 - Base (✅ Concluída)
- [x] Sistema de autenticação
- [x] Interface base
- [x] Sistema de toasts
- [x] Temas personalizados

### Fase 2 - RPG Core (🚧 Em desenvolvimento)
- [ ] Sistema de personagens
- [ ] Classes e habilidades
- [ ] Sistema de atributos
- [ ] Inventário

### Fase 3 - Gameplay (📋 Planejado)
- [ ] Sistema de combate
- [ ] Missões e quests
- [ ] Sistema de progressão
- [ ] Multiplayer básico

### Fase 4 - Avançado (🔮 Futuro)
- [ ] Guild system
- [ ] Eventos especiais
- [ ] Sistema de crafting
- [ ] API REST

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/NovaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona NovaFeature'`)
4. Push para a branch (`git push origin feature/NovaFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para detalhes.

## 🎨 Créditos

- **Ícones**: [game-icons.net](https://game-icons.net) - Coleção Lorc
- **Inspiração**: Cultura oriental e jogos RPG clássicos
- **Fonts**: Google Fonts (Cinzel, Crimson Text)

---

**🗡️ Que a honra guie seus passos e a sombra proteja seus segredos! ⚔️**