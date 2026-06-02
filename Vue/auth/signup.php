<?php
$uiTheme = ui_theme();
$oldSignup = $_SESSION['old_signup'] ?? [];
unset($_SESSION['old_signup']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= app_name() ?> - Inscription</title>
  <link rel="stylesheet" href="public/css/style.css?v=<?= filemtime(__DIR__ . '/../../public/css/style.css') ?>">
  <script defer src="public/js/theme.js?v=<?= filemtime(__DIR__ . '/../../public/js/theme.js') ?>"></script>
</head>
<body class="login-body" data-theme="<?= htmlspecialchars($uiTheme, ENT_QUOTES, 'UTF-8') ?>" style="display:block;">
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <h1><?= htmlspecialchars(app_name()) ?></h1>
      <p>Creer un compte utilisateur pour acceder au systeme</p>
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

    <form method="POST" action="index.php?page=signup">
      <?= csrf_input() ?>
      <div class="auth-split-fields">
        <div class="form-group">
          <label for="prenom">Prenom</label>
          <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($oldSignup['prenom'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($oldSignup['nom'] ?? '') ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label for="signup_email">Adresse email</label>
        <input type="email" id="signup_email" name="email" value="<?= htmlspecialchars($oldSignup['email'] ?? '') ?>" placeholder="nom@exemple.com" required>
      </div>
      <div class="form-group">
        <label for="signup_role">Role</label>
        <select id="signup_role" name="role" required>
          <option value="admin" <?= ($oldSignup['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
          <option value="vendeur" <?= ($oldSignup['role'] ?? 'vendeur') === 'vendeur' ? 'selected' : '' ?>>Vendeur</option>
          <option value="fournisseur" <?= ($oldSignup['role'] ?? '') === 'fournisseur' ? 'selected' : '' ?>>Fournisseur</option>
        </select>
      </div>
      <div class="auth-split-fields">
        <div class="form-group">
          <label for="signup_password">Mot de passe</label>
          <input type="password" id="signup_password" name="mot_de_passe" placeholder="8 caracteres minimum" required>
        </div>
        <div class="form-group">
          <label for="signup_password_confirm">Confirmation</label>
          <input type="password" id="signup_password_confirm" name="mot_de_passe_confirmation" placeholder="Retapez le mot de passe" required>
        </div>
      </div>
      <p class="auth-note">Choisissez le role du compte a creer : administrateur, vendeur ou fournisseur.</p>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:8px;">
        Creer mon compte &rarr;
      </button>
    </form>

    <div class="auth-switch">
      <span>Vous avez deja un compte ?</span>
      <a href="index.php?page=login">Retour a la connexion</a>
    </div>
  </div>
</div>
</body>
</html>
