<?php
$cookiePageTitle = $cookiePageTitle ?? 'Consentement aux cookies';
$cookieRedirectTarget = $cookieRedirectTarget ?? 'index.php';
$cookieBlocked = $cookieBlocked ?? false;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($cookiePageTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="public/css/style.css?v=<?= filemtime(__DIR__ . '/../../public/css/style.css') ?>">
</head>
<body class="cookie-consent-body" style="display:block;">
  <div class="cookie-consent-shell">
    <div class="cookie-consent-ambient cookie-consent-ambient-a"></div>
    <div class="cookie-consent-ambient cookie-consent-ambient-b"></div>

    <section class="cookie-consent-stage" aria-labelledby="cookie-consent-title" aria-modal="true" role="dialog">
      <div class="cookie-consent-eyebrow"><?= $cookieBlocked ? 'Acces bloque' : 'Avant de continuer' ?></div>
      <h1 id="cookie-consent-title"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
      <p class="cookie-consent-copy">
        <?php if ($cookieBlocked): ?>
          L'application utilise des cookies essentiels pour la session utilisateur, la securite et les preferences d'interface. Tant qu'ils sont refuses, l'acces reste bloque.
        <?php else: ?>
          Cette application utilise des cookies essentiels pour la connexion, la protection CSRF et les preferences d'affichage. Vous devez les accepter pour continuer.
        <?php endif; ?>
      </p>

      <div class="cookie-consent-card">
        <div class="cookie-consent-list-title">Cookies utilises</div>
        <ul class="cookie-consent-list">
          <li>Session PHP pour l'authentification et la securite</li>
          <li>Preference d'interface pour memoriser le theme</li>
        </ul>
      </div>

      <div class="cookie-consent-actions">
        <form method="POST" action="index.php">
          <input type="hidden" name="cookie_action" value="accept">
          <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($cookieRedirectTarget, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-primary cookie-consent-btn">Accepter et continuer</button>
        </form>

        <form method="POST" action="index.php">
          <input type="hidden" name="cookie_action" value="refuse">
          <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($cookieRedirectTarget, ENT_QUOTES, 'UTF-8') ?>">
          <button type="submit" class="btn btn-danger cookie-consent-btn">Refuser</button>
        </form>
      </div>

      <p class="cookie-consent-footnote">
        <?= $cookieBlocked ? 'Acceptez les cookies pour debloquer l acces a l application.' : 'Si vous refusez, l application restera inaccessible.' ?>
      </p>
    </section>
  </div>
</body>
</html>
