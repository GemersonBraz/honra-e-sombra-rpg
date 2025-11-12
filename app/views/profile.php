<?php
$pageTitle = 'Meu Perfil';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$basePath = base_path();
$user = new User();
$userInfo = $user->getUserInfo($_SESSION['user_id'] ?? 0);
$userId = (int)($_SESSION['user_id'] ?? 0);

// Montar lista de avatares (uploads do usuário + padrões)
$uploadAvatars = [];
$defaultAvatars = [];
$defaultsDir = realpath(__DIR__ . '/../../public/img/avatars/defaults');
$uploadsDir = realpath(__DIR__ . '/../../public/img/avatars/uploads');
// Uploads do usuário
if ($uploadsDir && is_dir($uploadsDir) && $userId) {
  $files = glob($uploadsDir . '/user_' . $userId . '_*.{png,jpg,jpeg,webp}', GLOB_BRACE);
  if ($files) {
    usort($files, function($a, $b) { return filectime($b) <=> filectime($a); });
    foreach ($files as $f) {
      $uploadAvatars[] = 'img/avatars/uploads/' . basename($f);
    }
  }
}
// Padrões
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

$allAvatars = array_merge($uploadAvatars, $defaultAvatars);
$currentAvatar = $_SESSION['user_avatar'] ?? null;
?>

<main class="flex-1 bg-background">
  <div class="container mx-auto px-4 py-8">
    <h1 class="font-title text-3xl font-bold text-primary mb-6">Meu Perfil</h1>

    <div class="grid lg:grid-cols-2 gap-8">
      <!-- Atual Avatar -->
      <section class="card">
        <h2 class="card-title">Avatar e Identidade</h2>
        <div class="flex items-center gap-6">
          <div class="relative">
            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-border bg-surface flex items-center justify-center">
              <?php if ($currentAvatar): ?>
                <img src="<?= $basePath . $currentAvatar ?>" alt="Avatar" class="w-full h-full object-cover">
              <?php else: ?>
                <img src="<?= $basePath ?>img/icons-1x1/lorc/visored-helm.svg" alt="Avatar" class="w-12 h-12 icon-primary">
              <?php endif; ?>
            </div>
          </div>
          <div class="flex-1 space-y-2 text-sm font-heading">
            <p><strong>Nome:</strong> <span class="text-primary font-medium"><?= htmlspecialchars($_SESSION['user_nome'] ?? '') ?></span></p>
            <?php if (!empty($userInfo['display_title'])): ?>
              <p><strong>Nome de Exibição:</strong> <span class="text-secondary font-medium"><?= htmlspecialchars($userInfo['display_title']) ?></span></p>
            <?php endif; ?>
            <p><strong>Email:</strong> <span class="text-primary font-medium"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span></p>
            <?php if (!empty($userInfo['bio'])): ?>
              <div>
                <p class="text-text/60 leading-snug"><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($userInfo['bio'])) ?></p>
              </div>
            <?php else: ?>
              <p class="text-text/50 italic">Sem descrição ainda.</p>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- Atualizar Avatar -->
      <section class="card">
        <h2 class="card-title">Atualizar Avatar</h2>
        <p class="card-description">Envie uma imagem (JPG, PNG, WEBP até 2MB) ou escolha um avatar salvo/padrão.</p>

        <form method="post" action="index.php?page=perfil" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="avatar">
          <div>
            <label for="avatar_upload" class="block font-heading text-sm mb-1">Enviar nova imagem</label>
            <input type="file" name="avatar_upload" id="avatar_upload" accept="image/jpeg,image/png,image/webp" class="form-input">
          </div>

          <?php if (!empty($allAvatars)): ?>
          <div>
            <p class="font-heading text-sm mb-2">Ou escolha um avatar salvo/padrão</p>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
              <?php foreach ($allAvatars as $i => $avatar): $id = 'preset_' . $i; ?>
                <div class="relative group">
                  <label for="<?= $id ?>" class="cursor-pointer block">
                    <input type="radio" name="avatar_preset" id="<?= $id ?>" value="<?= htmlspecialchars($avatar) ?>" class="hidden" <?= ($currentAvatar === $avatar) ? 'checked' : '' ?>>
                    <div class="avatar-option rounded-lg border-2 border-border hover:border-primary p-2 flex items-center justify-center">
                      <img src="<?= $basePath . $avatar ?>" alt="Avatar" class="w-16 h-16 object-contain">
                    </div>
                  </label>
                  <?php if (strpos($avatar, 'img/avatars/uploads/user_') === 0): ?>
                    <form method="post" action="index.php?page=perfil" class="absolute -top-2 -right-2 hidden group-hover:block">
                      <input type="hidden" name="action" value="delete_avatar">
                      <input type="hidden" name="delete_file" value="<?= htmlspecialchars($avatar) ?>">
                      <button type="submit" class="avatar-del-btn" title="Remover este upload">×</button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/checked-shield.svg" alt="Salvar" class="w-4 h-4 icon-white">Salvar Avatar
            </button>
          </div>
        </form>
      </section>

      <!-- Informações Públicas -->
      <section class="card lg:col-span-2">
        <h2 class="card-title">Informações Públicas</h2>
        <form method="post" action="index.php?page=perfil" class="space-y-4">
          <input type="hidden" name="action" value="profile">
          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label for="display_title" class="block font-heading text-sm mb-1">Nome de exibição</label>
              <input type="text" name="display_title" id="display_title" maxlength="40" class="form-input" value="<?= htmlspecialchars($userInfo['display_title'] ?? '') ?>" placeholder="Como os outros verão seu nome">
            </div>
          </div>
          <div>
            <label for="bio" class="block font-heading text-sm mb-1">Descrição (até 500 caracteres)</label>
            <textarea name="bio" id="bio" rows="4" maxlength="500" class="form-input" placeholder="Conte um pouco sobre você..."><?= htmlspecialchars($userInfo['bio'] ?? '') ?></textarea>
          </div>
          <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/checked-shield.svg" alt="Salvar" class="w-4 h-4 icon-white">Salvar Perfil
            </button>
          </div>
        </form>
      </section>

      <!-- Segurança -->
      <section class="card lg:col-span-2">
        <h2 class="card-title">Segurança</h2>
        <form method="post" action="index.php?page=perfil" class="space-y-4">
          <input type="hidden" name="action" value="change_password">
          <div class="grid md:grid-cols-3 gap-4">
            <div>
              <label for="current_password" class="block font-heading text-sm mb-1">Senha atual</label>
              <input type="password" name="current_password" id="current_password" class="form-input" required>
            </div>
            <div>
              <label for="new_password" class="block font-heading text-sm mb-1">Nova senha</label>
              <input type="password" name="new_password" id="new_password" class="form-input" required>
            </div>
            <div>
              <label for="confirm_password" class="block font-heading text-sm mb-1">Confirmar nova senha</label>
              <input type="password" name="confirm_password" id="confirm_password" class="form-input" required>
            </div>
          </div>
          <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-secondary inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/key.svg" alt="Salvar" class="w-4 h-4 icon-white">Alterar Senha
            </button>
            <a href="index.php?page=dashboard" class="btn-outline inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/wooden-door.svg" alt="Voltar" class="w-4 h-4 icon-primary">Voltar ao Dashboard
            </a>
          </div>
        </form>
      </section>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
