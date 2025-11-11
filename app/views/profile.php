<?php
$pageTitle = 'Meu Perfil';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$basePath = base_path();

// Montar lista de avatares padrões (se existir pasta)
$defaultAvatars = [];
$defaultsDir = realpath(__DIR__ . '/../../public/img/avatars/defaults');
if ($defaultsDir && is_dir($defaultsDir)) {
    $files = glob($defaultsDir . '/*.{png,jpg,jpeg,webp,svg}', GLOB_BRACE);
    foreach ($files as $f) {
        $defaultAvatars[] = 'img/avatars/defaults/' . basename($f);
    }
}
// Fallback para alguns ícones existentes, se não houver defaults
if (empty($defaultAvatars)) {
    $fallback = [
        'img/icons-1x1/lorc/visored-helm.svg',
        'img/icons-1x1/lorc/ninja-mask.svg',
        'img/icons-1x1/lorc/bordered-shield.svg',
        'img/icons-1x1/lorc/dragon-head.svg',
    ];
    foreach ($fallback as $f) {
        if (file_exists(__DIR__ . '/../../public/' . $f)) {
            $defaultAvatars[] = $f;
        }
    }
}

$currentAvatar = $_SESSION['user_avatar'] ?? null;
?>

<main class="flex-1 bg-background">
  <div class="container mx-auto px-4 py-8">
    <h1 class="font-title text-3xl font-bold text-primary mb-6">Meu Perfil</h1>

    <div class="grid lg:grid-cols-2 gap-8">
      <!-- Atual Avatar -->
      <section class="card">
        <h2 class="card-title">Avatar Atual</h2>
        <div class="flex items-center gap-4">
          <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-border bg-surface flex items-center justify-center">
            <?php if ($currentAvatar): ?>
              <img src="<?= $basePath . $currentAvatar ?>" alt="Avatar" class="w-full h-full object-cover">
            <?php else: ?>
              <img src="<?= $basePath ?>img/icons-1x1/lorc/visored-helm.svg" alt="Avatar" class="w-12 h-12 icon-primary">
            <?php endif; ?>
          </div>
          <div class="text-text/80">
            <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['user_nome'] ?? '') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>
          </div>
        </div>
      </section>

      <!-- Atualizar Avatar -->
      <section class="card">
        <h2 class="card-title">Atualizar Avatar</h2>
        <p class="card-description">Envie uma imagem (JPG, PNG, WEBP até 2MB) ou escolha um avatar padrão.</p>

        <form method="post" action="index.php?page=perfil" enctype="multipart/form-data" class="space-y-4">
          <div>
            <label for="avatar_upload" class="block font-heading text-sm mb-1">Enviar nova imagem</label>
            <input type="file" name="avatar_upload" id="avatar_upload" accept="image/jpeg,image/png,image/webp" class="form-input">
          </div>

          <?php if (!empty($defaultAvatars)): ?>
          <div>
            <p class="font-heading text-sm mb-2">Ou escolha um avatar padrão</p>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
              <?php foreach ($defaultAvatars as $i => $avatar): $id = 'preset_' . $i; ?>
                <label for="<?= $id ?>" class="cursor-pointer">
                  <input type="radio" name="avatar_preset" id="<?= $id ?>" value="<?= htmlspecialchars($avatar) ?>" class="hidden">
                  <div class="rounded-lg border-2 border-border hover:border-primary p-2 flex items-center justify-center">
                    <img src="<?= $basePath . $avatar ?>" alt="Avatar" class="w-16 h-16 object-contain">
                  </div>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/checked-shield.svg" alt="Salvar" class="w-4 h-4 icon-white">Salvar Alterações
            </button>
            <a href="index.php?page=dashboard" class="btn-outline inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/wooden-door.svg" alt="Voltar" class="w-4 h-4 icon-primary">Voltar
            </a>
          </div>
        </form>
      </section>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
