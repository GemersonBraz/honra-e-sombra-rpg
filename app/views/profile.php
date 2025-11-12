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
      <!-- Avatar e Identidade -->
      <section class="card lg:col-span-2">
        <h2 class="card-title">Avatar e Identidade</h2>
        <div class="grid md:grid-cols-3 gap-6">
          <!-- Avatar -->
          <div class="flex flex-col items-center gap-4">
            <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-border bg-surface flex items-center justify-center">
              <?php if ($currentAvatar): ?>
                <img src="<?= $basePath . $currentAvatar ?>" alt="Avatar" class="w-full h-full object-cover">
              <?php else: ?>
                <img src="<?= $basePath ?>img/icons-1x1/lorc/visored-helm.svg" alt="Avatar" class="w-16 h-16 icon-primary">
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Dados Pessoais -->
          <div>
            <h4 class="font-heading font-medium text-primary mb-3 flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/profile.svg" alt="Dados" class="w-4 h-4 icon-primary">
              Dados Pessoais
            </h4>
            <div class="space-y-2 text-sm">
              <div class="flex flex-col">
                <span class="text-text/60">Nome:</span>
                <span class="text-primary font-medium"><?= htmlspecialchars($userInfo['nome']) ?></span>
              </div>
              <div class="flex flex-col">
                <span class="text-text/60">Email:</span>
                <span class="text-primary font-medium"><?= htmlspecialchars($userInfo['email']) ?></span>
              </div>
              <div class="flex flex-col">
                <span class="text-text/60">Tipo de Conta:</span>
                <span class="text-primary font-medium"><?= ucfirst($userInfo['tipo']) ?></span>
              </div>
            </div>
          </div>
          
          <!-- Estatísticas -->
          <div>
            <h4 class="font-heading font-medium text-primary mb-3 flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/chart.svg" alt="Stats" class="w-4 h-4 icon-primary">
              Estatísticas
            </h4>
            <div class="space-y-2 text-sm">
              <div class="flex flex-col">
                <span class="text-text/60">Conta criada em:</span>
                <span class="text-primary font-medium"><?= date('d/m/Y', strtotime($userInfo['data_criacao'])) ?></span>
              </div>
              <div class="flex flex-col">
                <span class="text-text/60">Último acesso:</span>
                <span class="text-primary font-medium"><?= date('d/m/Y H:i', strtotime($userInfo['ultimo_login'] ?? 'now')) ?></span>
              </div>
              <div class="flex flex-col">
                <span class="text-text/60">Status:</span>
                <span class="text-emerald-600 font-medium flex items-center gap-1">
                  <img src="<?= $basePath ?>img/icons-1x1/lorc/checked-shield.svg" alt="Ativa" class="w-4 h-4" style="filter: brightness(0) saturate(100%) invert(45%) sepia(86%) saturate(492%) hue-rotate(95deg) brightness(101%) contrast(101%);">
                  Ativa
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Ações -->
        <div class="mt-6 pt-6 border-t border-border">
          <div class="flex gap-3">
            <button onclick="openProfileModal()" class="btn-primary inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/quill-ink.svg" alt="Editar" class="w-4 h-4 icon-white">
              Editar Perfil
            </button>
            <a href="index.php?page=dashboard" class="btn-outline inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/wooden-door.svg" alt="Voltar" class="w-4 h-4 icon-primary">
              Voltar ao Dashboard
            </a>
          </div>
        </div>
      </section>
      
      <!-- Informações Públicas -->
      <section class="card lg:col-span-2">
        <h2 class="card-title">Informações Públicas</h2>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <h4 class="font-heading font-medium text-primary mb-2">Nome de Exibição</h4>
            <?php if (!empty($userInfo['display_title'])): ?>
              <p class="text-secondary font-medium text-lg"><?= htmlspecialchars($userInfo['display_title']) ?></p>
            <?php else: ?>
              <p class="text-text/50 italic text-sm">Não definido</p>
            <?php endif; ?>
          </div>
          <div>
            <h4 class="font-heading font-medium text-primary mb-2">Descrição</h4>
            <?php if (!empty($userInfo['bio'])): ?>
              <p class="text-text/70 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($userInfo['bio'])) ?></p>
            <?php else: ?>
              <p class="text-text/50 italic text-sm">Sem descrição ainda.</p>
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
            <a href="index.php?page=dashboard" class="btn-outline inline-flex items-center gap-2">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/wooden-door.svg" alt="Voltar" class="w-4 h-4 icon-primary">Voltar ao Dashboard
            </a>
          </div>
        </form>
      </section>
    </div>
    
    <!-- Modal de Edição de Perfil -->
    <div id="profileModal" class="modal" style="display: none;">
      <div class="modal-overlay" onclick="closeProfileModal()"></div>
      <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
          <h3 class="font-heading text-xl font-semibold text-primary">Editar Perfil</h3>
          <button onclick="closeProfileModal()" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <!-- Formulário de Informações Públicas -->
          <form id="profileInfoForm" method="post" action="index.php?page=perfil" class="space-y-4 mb-6 pb-6 border-b border-border">
            <input type="hidden" name="action" value="profile">
            <h4 class="font-heading font-medium text-primary mb-3">Informações Públicas</h4>
            <div>
              <label for="modal_display_title" class="block font-heading text-sm mb-1">Nome de exibição</label>
              <input type="text" name="display_title" id="modal_display_title" maxlength="40" class="form-input" value="<?= htmlspecialchars($userInfo['display_title'] ?? '') ?>" placeholder="Como os outros verão seu nome">
            </div>
            <div>
              <label for="modal_bio" class="block font-heading text-sm mb-1">Descrição (até 500 caracteres)</label>
              <textarea name="bio" id="modal_bio" rows="4" maxlength="500" class="form-input" placeholder="Conte um pouco sobre você..."><?= htmlspecialchars($userInfo['bio'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn-primary inline-flex items-center gap-2 w-full justify-center">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/checked-shield.svg" alt="Salvar" class="w-4 h-4 icon-white">
              Salvar Informações
            </button>
          </form>
          
          <!-- Formulário de Alteração de Senha -->
          <form id="passwordForm" method="post" action="index.php?page=perfil" class="space-y-4">
            <input type="hidden" name="action" value="change_password">
            <h4 class="font-heading font-medium text-primary mb-3">Alterar Senha</h4>
            <div>
              <label for="modal_current_password" class="block font-heading text-sm mb-1">Senha atual</label>
              <input type="password" name="current_password" id="modal_current_password" class="form-input" required>
            </div>
            <div>
              <label for="modal_new_password" class="block font-heading text-sm mb-1">Nova senha</label>
              <input type="password" name="new_password" id="modal_new_password" class="form-input" required>
            </div>
            <div>
              <label for="modal_confirm_password" class="block font-heading text-sm mb-1">Confirmar nova senha</label>
              <input type="password" name="confirm_password" id="modal_confirm_password" class="form-input" required>
            </div>
            <button type="submit" class="btn-secondary inline-flex items-center gap-2 w-full justify-center">
              <img src="<?= $basePath ?>img/icons-1x1/lorc/key.svg" alt="Alterar" class="w-4 h-4 icon-white">
              Alterar Senha
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
function openProfileModal() {
  const modal = document.getElementById('profileModal');
  if (modal) {
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.opacity = '1';
    document.body.style.overflow = 'hidden';
  }
}

function closeProfileModal() {
  const modal = document.getElementById('profileModal');
  if (modal) {
    modal.style.opacity = '0';
    setTimeout(() => {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }, 300);
    // Limpar campos de senha
    const currentPwd = document.getElementById('modal_current_password');
    const newPwd = document.getElementById('modal_new_password');
    const confirmPwd = document.getElementById('modal_confirm_password');
    if (currentPwd) currentPwd.value = '';
    if (newPwd) newPwd.value = '';
    if (confirmPwd) confirmPwd.value = '';
  }
}

// Fechar modal ao pressionar ESC
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeProfileModal();
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
