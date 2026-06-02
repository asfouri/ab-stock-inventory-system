<?php $pageTitle = 'Detail vente'; include 'Vue/partials/layout.php'; ?>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <h2 class="page-title">Vente - <?= htmlspecialchars($vente['reference']) ?></h2>
    <p class="page-subtitle"><?= date('d/m/Y \a\ H:i', strtotime($vente['created_at'])) ?> - Vendeur : <?= htmlspecialchars($vente['vendeur_nom']) ?></p>
  </div>
  <div style="display:flex; gap:10px; flex-wrap:wrap;">
    <button onclick="window.print()" class="btn btn-secondary">Imprimer</button>
    <a href="index.php?page=historique" class="btn btn-secondary">Retour</a>
  </div>
</div>

<div style="display:grid; grid-template-columns:1fr 320px; gap:24px;">
  <div class="card">
    <h3 style="font-family:'Manrope',sans-serif; font-weight:800; margin-bottom:16px;">Produits vendus</h3>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Produit</th>
            <th>Prix unitaire</th>
            <th>Quantite</th>
            <th>Sous-total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vente['lignes'] as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['produit_nom']) ?></td>
            <td><?= number_format($l['prix_unitaire'], 2, ',', ' ') ?> &euro;</td>
            <td><?= $l['quantite'] ?></td>
            <td style="font-weight:700;"><?= number_format($l['sous_total'], 2, ',', ' ') ?> &euro;</td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card" style="height:fit-content;">
    <h3 style="font-family:'Manrope',sans-serif; font-weight:800; margin-bottom:16px;">Recapitulatif</h3>
    <div style="display:flex; flex-direction:column; gap:10px;">
      <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
        <span style="color:var(--text-muted);">Reference</span>
        <code style="color:var(--accent-strong);"><?= htmlspecialchars($vente['reference']) ?></code>
      </div>
      <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
        <span style="color:var(--text-muted);">Date</span>
        <span><?= date('d/m/Y H:i', strtotime($vente['created_at'])) ?></span>
      </div>
      <hr>
      <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
        <span style="color:var(--text-muted);">Total HT</span>
        <span><?= number_format($vente['total_ht'], 2, ',', ' ') ?> &euro;</span>
      </div>
      <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
        <span style="color:var(--text-muted);">TVA (<?= $vente['taux_tva'] ?>%)</span>
        <span><?= number_format($vente['total_ttc'] - $vente['total_ht'], 2, ',', ' ') ?> &euro;</span>
      </div>
      <hr>
      <div style="display:flex; justify-content:space-between; font-size:1.08rem; font-weight:800;">
        <span>Total TTC</span>
        <span style="color:var(--accent-strong);"><?= number_format($vente['total_ttc'], 2, ',', ' ') ?> &euro;</span>
      </div>
    </div>
  </div>
</div>

</main></body></html>
