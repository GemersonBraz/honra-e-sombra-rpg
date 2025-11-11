<?php
$pageTitle = 'Entrar';
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
                            <img src="img/icons-1x1/lorc/crossed-swords.svg" alt="Espadas Cruzadas" class="w-8 h-8 icon-primary">
                        </div>
                    </div>
                    <h1 class="font-title text-3xl font-bold text-primary">Acesso ao Reino</h1>
                    <p class="font-heading text-text/70 mt-2">Entre com suas credenciais de guerreiro</p>
                </div>
                
                <form method="POST" action="index.php?page=login" class="space-y-6">
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
                               placeholder="Sua senha secreta"
                               required>
                    </div>
                    
                    <button type="submit" 
                            class="btn-primary w-full flex items-center justify-center">
                        <img src="img/icons-1x1/lorc/magic-gate.svg" alt="Entrar" class="w-5 h-5 icon-white mr-2">
                        Entrar no Reino
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-text/70 font-heading">
                        Ainda não possui uma conta? 
                        <a href="index.php?page=register" class="text-primary hover:text-accent font-medium flex items-center justify-center">
                            <img src="img/icons-1x1/lorc/edged-shield.svg" alt="Registrar" class="w-4 h-4 icon-primary mr-1">
                            Junte-se à Ordem
                        </a>
                    </p>
                </div>
                
                <div class="mt-6 pt-6 border-t border-border">
                    <div class="text-center">
                        <p class="text-text/70 text-sm font-heading mb-2">Conta de teste:</p>
                        <div class="text-xs text-text/60 font-heading">
                            <p><strong>Admin:</strong> admin@honraesombra.com / admin123</p>
                            <p><strong>Usuário:</strong> usuario@teste.com / user123</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>