<?php $uiTheme = ui_theme(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= app_name() ?> - Connexion</title>
  <link rel="stylesheet" href="public/css/style.css?v=<?= filemtime(__DIR__ . '/../../public/css/style.css') ?>">
  <script defer src="public/js/theme.js?v=<?= filemtime(__DIR__ . '/../../public/js/theme.js') ?>"></script>
</head>
<body class="login-body" data-theme="<?= htmlspecialchars($uiTheme, ENT_QUOTES, 'UTF-8') ?>" style="display:block;">
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <h1><?= htmlspecialchars(app_name()) ?></h1>
      <p>Plateforme de gestion des ventes et des stocks</p>
    </div>

    <div class="theme-switcher theme-switcher-login">
      <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="<?= $uiTheme === 'dark' ? 'true' : 'false' ?>">
        <span class="theme-toggle-mark" data-theme-toggle-mark><?= $uiTheme === 'dark' ? 'DK' : 'LT' ?></span>
        <span class="theme-toggle-copy">
          <span class="theme-toggle-state" data-theme-toggle-state><?= $uiTheme === 'dark' ? 'Sombre actif' : 'Clair actif' ?></span>
          <span class="theme-toggle-label" data-theme-toggle-label><?= $uiTheme === 'dark' ? 'Mode clair' : 'Mode sombre' ?></span>
        </span>
      </button>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?page=login" id="login-form">
      <?= csrf_input() ?>
      <div class="form-group">
        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" placeholder="votre@email.com" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="********" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:8px;">
        Se connecter &rarr;
      </button>
    </form>

    <div class="auth-switch">
      <span>Vous n'avez pas encore de compte ?</span>
      <a href="index.php?page=signup">Creer un compte</a>
    </div>

    <div class="demo-accounts">
      <p class="demo-accounts-title">Acces rapide</p>
      <div class="demo-accounts-grid">
        <button type="button" class="demo-account-btn" data-email="admin@magasin.com" data-password="password">
          <span class="demo-account-role demo-account-role-admin">Admin</span>
        </button>
        <button type="button" class="demo-account-btn" data-email="vendeur@magasin.com" data-password="password">
          <span class="demo-account-role demo-account-role-vendeur">Vendeur</span>
        </button>
        <button type="button" class="demo-account-btn" data-email="fournisseur@magasin.com" data-password="password">
          <span class="demo-account-role demo-account-role-fournisseur">Fournisseur</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const demoButtons = document.querySelectorAll('.demo-account-btn');

  demoButtons.forEach((button) => {
    button.addEventListener('click', () => {
      emailInput.value = button.dataset.email || '';
      passwordInput.value = button.dataset.password || '';
      emailInput.dispatchEvent(new Event('input', { bubbles: true }));
      passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
      passwordInput.focus();
    });
  });
</script>
</body>
</html>
