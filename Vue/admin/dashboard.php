<?php $pageTitle = 'Dashboard'; include 'Vue/partials/layout.php'; ?>

<div class="page-header">
  <h2 class="page-title">Dashboard</h2>
  <p class="page-subtitle">Vue d'ensemble du magasin - <?= date('d/m/Y') ?></p>
</div>

<div class="stats-grid">
  <div class="stat-card" style="--accent-color: var(--accent);">
    <div class="stat-label">Chiffre d'affaires total</div>
    <div class="stat-value"><?= number_format($ca['ca_total'] ?? 0, 2, ',', ' ') ?> &euro;</div>
    <div class="stat-sub"><?= $ca['nb_ventes'] ?? 0 ?> ventes realisees</div>
  </div>
  <div class="stat-card" style="--accent-color: var(--accent2);">
    <div class="stat-label">Produits en catalogue</div>
    <div class="stat-value"><?= $nbProduits ?></div>
    <div class="stat-sub">produits actifs</div>
  </div>
  <div class="stat-card" style="--accent-color: var(--warning);">
    <div class="stat-label">Valeur du stock</div>
    <div class="stat-value"><?= number_format($stockValue, 0, ',', ' ') ?> &euro;</div>
    <div class="stat-sub">au prix d'achat</div>
  </div>
  <div class="stat-card" style="--accent-color: <?= count($lowStock) > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
    <div class="stat-label">Alertes de stock</div>
    <div class="stat-value" style="color:<?= count($lowStock) > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
      <?= count($lowStock) ?>
    </div>
    <div class="stat-sub">produit(s) sous le seuil</div>
  </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:28px;">
  <div class="card">
    <h3 style="font-family:'Manrope',sans-serif; font-weight:800; margin-bottom:20px;">CA mensuel</h3>
    <div class="chart-wrap">
      <canvas id="caChart"></canvas>
    </div>
  </div>

  <div class="card">
    <h3 style="font-family:'Manrope',sans-serif; font-weight:800; margin-bottom:20px;">Top 5 produits vendus</h3>
    <?php if (empty($topProduits)): ?>
      <p style="color:var(--text-muted);">Aucune vente enregistree.</p>
    <?php else: ?>
      <?php foreach ($topProduits as $i => $p): ?>
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
        <span style="width:30px; height:30px; background:var(--surface-2); border:1px solid var(--border); border-radius:999px; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; color:var(--text-muted);">
          <?= $i + 1 ?>
        </span>
        <div style="flex:1;">
          <div style="font-size:0.92rem; font-weight:700; color:var(--text);"><?= htmlspecialchars($p['nom']) ?></div>
          <div style="font-size:0.8rem; color:var(--text-muted);"><?= $p['total_vendu'] ?> vendus</div>
        </div>
        <div style="font-weight:800; color:var(--accent-strong); font-size:0.92rem;">
          <?= number_format($p['ca'], 2, ',', ' ') ?> &euro;
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($lowStock)): ?>
<div class="alert-stock-banner">
  <span class="icon">AL</span>
  <div>
    <strong><?= count($lowStock) ?> produit(s) sous le seuil d'alerte</strong>
    <div style="font-size:0.82rem; color:var(--text-muted); margin-top:2px;">
      <?= implode(', ', array_slice(array_column($lowStock, 'nom'), 0, 5)) ?>
      <?= count($lowStock) > 5 ? '...' : '' ?>
    </div>
  </div>
  <a href="index.php?page=produits" class="btn btn-sm btn-secondary" style="margin-left:auto;">
    Voir les produits
  </a>
</div>
<?php endif; ?>

<div class="card">
  <h3 style="font-family:'Manrope',sans-serif; font-weight:800; margin-bottom:16px;">Produits en rupture ou stock critique</h3>
  <?php if (empty($lowStock)): ?>
    <p style="color:var(--text-muted);">Tous les stocks sont au-dessus des seuils d'alerte.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Produit</th>
          <th>Categorie</th>
          <th>Stock actuel</th>
          <th>Seuil d'alerte</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lowStock as $p): ?>
        <tr>
          <td><strong><?= htmlspecialchars($p['nom']) ?></strong></td>
          <td><?= htmlspecialchars($p['categorie_nom'] ?? '-') ?></td>
          <td style="font-weight:800; color:<?= $p['quantite_stock'] == 0 ? 'var(--danger)' : 'var(--warning)' ?>">
            <?= $p['quantite_stock'] ?>
          </td>
          <td><?= $p['seuil_alerte'] ?></td>
          <td>
            <?php if ($p['quantite_stock'] == 0): ?>
              <span class="badge badge-danger">Rupture</span>
            <?php else: ?>
              <span class="badge badge-warning">Stock faible</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<script>
const ctx = document.getElementById('caChart').getContext('2d');
const caData = <?= json_encode(array_reverse($caMois)) ?>;

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: caData.map(d => d.mois),
    datasets: [{
      label: 'CA (EUR)',
      data: caData.map(d => parseFloat(d.ca)),
      backgroundColor: 'rgba(37, 99, 235, 0.16)',
      borderColor: '#2563eb',
      borderWidth: 1.5,
      borderRadius: 10,
      hoverBackgroundColor: 'rgba(37, 99, 235, 0.24)'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { color: '#64748b' }
      },
      y: {
        grid: { color: 'rgba(148, 163, 184, 0.18)' },
        ticks: { color: '#64748b' }
      }
    }
  }
});
</script>

</main></body></html>
