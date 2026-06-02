<?php
$currentPage = $_GET['page'] ?? '';
$role = $_SESSION['user_role'] ?? '';
$nom = $_SESSION['user_nom'] ?? 'Utilisateur';
$initiales = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $nom)));
$uiTheme = ui_theme();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= app_name() ?> - <?= htmlspecialchars($pageTitle ?? 'App') ?></title>
  <link rel="stylesheet" href="public/css/style.css?v=<?= filemtime(__DIR__ . '/../../public/css/style.css') ?>">
  <script defer src="public/js/theme.js?v=<?= filemtime(__DIR__ . '/../../public/js/theme.js') ?>"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body data-theme="<?= htmlspecialchars($uiTheme, ENT_QUOTES, 'UTF-8') ?>">
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="brand-mark">GS</div>
    <div>
      <h1><?= htmlspecialchars(app_name()) ?></h1>
      <span>Gestion commerciale</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php if ($role === 'admin'): ?>
    <div class="nav-section">
      <div class="nav-section-title">Vue generale</div>
      <a href="index.php?page=dashboard" class="nav-link <?= $currentPage === 'dashboard' || $currentPage === '' ? 'active' : '' ?>">
        <span class="icon">DB</span><span>Dashboard</span>
      </a>
    </div>
    <?php endif; ?>

    <div class="nav-section">
      <div class="nav-section-title">Catalogue</div>
      <a href="index.php?page=produits" class="nav-link <?= $currentPage === 'produits' ? 'active' : '' ?>">
        <span class="icon">PR</span><span>Produits</span>
      </a>
      <?php if ($role === 'admin'): ?>
      <a href="index.php?page=categories" class="nav-link <?= $currentPage === 'categories' ? 'active' : '' ?>">
        <span class="icon">CA</span><span>Categories</span>
      </a>
      <a href="index.php?page=fournisseurs" class="nav-link <?= $currentPage === 'fournisseurs' ? 'active' : '' ?>">
        <span class="icon">FO</span><span>Fournisseurs</span>
      </a>
      <?php endif; ?>
    </div>

    <?php if (in_array($role, ['admin', 'vendeur'], true)): ?>
    <div class="nav-section">
      <div class="nav-section-title">Ventes</div>
      <a href="index.php?page=caisse" class="nav-link <?= $currentPage === 'caisse' ? 'active' : '' ?>">
        <span class="icon">CS</span><span>Caisse</span>
      </a>
      <a href="index.php?page=historique" class="nav-link <?= $currentPage === 'historique' ? 'active' : '' ?>">
        <span class="icon">HV</span><span>Historique</span>
      </a>
    </div>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
    <div class="nav-section">
      <div class="nav-section-title">Administration</div>
      <a href="index.php?page=utilisateurs" class="nav-link <?= $currentPage === 'utilisateurs' ? 'active' : '' ?>">
        <span class="icon">US</span><span>Utilisateurs</span>
      </a>
      <a href="index.php?page=parametres" class="nav-link <?= $currentPage === 'parametres' ? 'active' : '' ?>">
        <span class="icon">CF</span><span>Configuration</span>
      </a>
    </div>
    <?php endif; ?>
  </nav>

  <div class="sidebar-user">
    <div class="theme-switcher">
      <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="<?= $uiTheme === 'dark' ? 'true' : 'false' ?>">
        <span class="theme-toggle-mark" data-theme-toggle-mark><?= $uiTheme === 'dark' ? 'DK' : 'LT' ?></span>
        <span class="theme-toggle-copy">
          <span class="theme-toggle-state" data-theme-toggle-state><?= $uiTheme === 'dark' ? 'Sombre actif' : 'Clair actif' ?></span>
          <span class="theme-toggle-label" data-theme-toggle-label><?= $uiTheme === 'dark' ? 'Mode clair' : 'Mode sombre' ?></span>
        </span>
      </button>
    </div>
    <div class="user-info">
      <div class="user-avatar"><?= $initiales ?></div>
      <div class="user-details">
        <div class="user-name"><?= htmlspecialchars($nom) ?></div>
        <div class="user-role"><?= htmlspecialchars($role) ?></div>
      </div>
      <form method="POST" action="index.php?page=logout" style="margin-left:auto;">
        <?= csrf_input() ?>
        <button type="submit" class="logout-btn" title="Deconnexion">Quitter</button>
      </form>
    </div>
  </div>
</aside>

<main class="main">
<?php
if (!empty($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}
if (!empty($_SESSION['error'])) {
    echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}
?>
