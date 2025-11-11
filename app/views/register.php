<?php
$pageTitle = 'Criar Conta';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<!-- Main Content -->
<main class="flex-1 theme-bg-background theme-transition">
    <div class="container mx-auto px-4 py-8">
        
        <div class="max-w-md mx-auto">
            <div class="card p-8 border-2 border-border/50 shadow-2xl">
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">
                            <img src="img/icons-1x1/lorc/edged-shield.svg" alt="Escudo" class="w-8 h-8 icon-primary">
                        </div>
                    </div>
                    <h1 class="font-title text-3xl font-bold text-primary">Juntar-se à Ordem</h1>
                    <p class="font-heading text-text/70 mt-2">Crie sua conta e inicie sua jornada</p>
                </div>
                
                <form method="POST" action="index.php?page=register" class="space-y-6">
                    <div>
                        <label for="nome" class="block font-heading font-medium text-primary mb-2 flex items-center">
                            <img src="img/icons-1x1/lorc/visored-helm.svg" alt="Nome" class="w-4 h-4 icon-primary mr-2">
                            Nome do Guerreiro
                        </label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                               class="form-input"
                               placeholder="Seu nome completo"
                               required>
                    </div>
                    
                    <div>
                        <label for="email" class="block font-heading font-medium text-primary mb-2 flex items-center">
                            <img src="img/icons-1x1/lorc/scroll-unfurled.svg" alt="Email" class="w-4 h-4 icon-primary mr-2">
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               class="form-input"
                               placeholder="seu@email.com"
                               required>
                    </div>
                    
                    <div>
                        <label for="senha" class="block font-heading font-medium text-primary mb-2 flex items-center">
                            <img src="img/icons-1x1/lorc/key.svg" alt="Senha" class="w-4 h-4 icon-primary mr-2">
                            Senha
                        </label>
                        <input type="password" 
                               id="senha" 
                               name="senha" 
                               class="form-input"
                               placeholder="Mínimo 6 caracteres"
                               minlength="6"
                               required>
                    </div>
                    
                    <div>
                        <label for="confirmar_senha" class="block font-heading font-medium text-primary mb-2 flex items-center">
                            <img src="img/icons-1x1/lorc/three-keys.svg" alt="Confirmar Senha" class="w-4 h-4 icon-primary mr-2">
                            Confirmar Senha
                        </label>
                        <input type="password" 
                               id="confirmar_senha" 
                               name="confirmar_senha" 
                               class="form-input"
                               placeholder="Digite novamente a senha"
                               required>
                    </div>
                    
                    <button type="submit" 
                            class="btn-primary w-full flex items-center justify-center">
                        <img src="img/icons-1x1/lorc/beveled-star.svg" alt="Criar Conta" class="w-5 h-5 icon-white mr-2">
                        Criar Conta
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-text/70 font-heading">
                        Já possui uma conta? 
                        <a href="index.php?page=login" class="text-primary hover:text-accent font-medium flex items-center justify-center">
                            <img src="img/icons-1x1/lorc/crossed-swords.svg" alt="Login" class="w-4 h-4 icon-primary mr-1">
                            Fazer Login
                        </a>
                    </p>
                </div>
                
                <div class="mt-6 pt-6 border-t border-border">
                    <div class="text-center">
                        <p class="text-xs text-text/60 font-heading">
                            Ao criar uma conta, você aceita nossos termos de uso e política de privacidade.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>