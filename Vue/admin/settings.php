<?php $pageTitle = 'Configuration'; include 'Vue/partials/layout.php'; ?>

<div class="page-header">
  <h2 class="page-title">Configuration globale</h2>
  <p class="page-subtitle">Paramètres généraux de l'application et de la vente</p>
</div>

<div class="card" style="max-width:760px;">
  <form method="POST" action="index.php?page=parametres">
    <?= csrf_input() ?>
    <div class="form-row">
      <div class="form-group">
        <label>Nom de l'application *</label>
        <input type="text" name="app_name" required value="<?= htmlspecialchars($settings['app_name'] ?? APP_NAME) ?>">
      </div>
      <div class="form-group">
        <label>TVA (%) *</label>
        <input type="number" name="tva_taux" min="0" max="100" step="0.01" required value="<?= htmlspecialchars($settings['tva_taux'] ?? number_format(TVA_TAUX, 2, '.', '')) ?>">
      </div>
    </div>

    <div class="form-group">
      <label>URL de base *</label>
      <input type="url" name="base_url" required value="<?= htmlspecialchars($settings['base_url'] ?? BASE_URL) ?>">
    </div>

    <div class="alert alert-success" style="margin-bottom:18px;">
      Ces paramètres sont persistés en base et surchargent les constantes par défaut.
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Enregistrer la configuration</button>
    </div>
  </form>
</div>

</main></body></html>
