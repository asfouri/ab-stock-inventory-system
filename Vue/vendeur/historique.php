<?php $pageTitle = 'Historique des ventes'; include 'Vue/partials/layout.php'; ?>

<div class="page-header" style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <h2 class="page-title">Historique des ventes</h2>
    <p class="page-subtitle">
      <?= count($ventes) ?> vente(s) -
      Total : <?= number_format(array_sum(array_column($ventes, 'total_ttc')), 2, ',', ' ') ?> &euro;
    </p>
  </div>

  <?php if ($_SESSION['user_role'] === 'admin' && !empty($ventes)): ?>
  <form method="POST" action="index.php?page=historique&action=clear" onsubmit="return confirm('Supprimer tout l historique des ventes ? Cette action est irreversible.');">
    <?= csrf_input() ?>
    <button type="submit" class="btn btn-danger">Supprimer l historique</button>
  </form>
  <?php endif; ?>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Reference</th>
        <th>Date</th>
        <?php if ($_SESSION['user_role'] === 'admin'): ?><th>Vendeur</th><?php endif; ?>
        <th>Total HT</th>
        <th>TVA</th>
        <th>Total TTC</th>
        <th>Statut</th>
        <th>Detail</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ventes as $v): ?>
      <tr>
        <td><code style="color:var(--accent-strong); font-size:0.8rem;"><?= htmlspecialchars($v['reference']) ?></code></td>
        <td><?= date('d/m/Y H:i', strtotime($v['created_at'])) ?></td>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <td><?= htmlspecialchars($v['vendeur_nom']) ?></td>
        <?php endif; ?>
        <td><?= number_format($v['total_ht'], 2, ',', ' ') ?> &euro;</td>
        <td><?= number_format($v['total_ttc'] - $v['total_ht'], 2, ',', ' ') ?> &euro;</td>
        <td style="font-weight:800; color:var(--accent-strong);"><?= number_format($v['total_ttc'], 2, ',', ' ') ?> &euro;</td>
        <td><span class="badge badge-success">Validee</span></td>
        <td>
          <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="index.php?page=detail_vente&id=<?= $v['id'] ?>" class="btn btn-sm btn-secondary">Voir</a>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <form method="POST" action="index.php?page=historique&action=delete" onsubmit="return confirm('Supprimer cette vente de l historique ?');" style="margin:0;">
              <?= csrf_input() ?>
              <input type="hidden" name="id" value="<?= $v['id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
            </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($ventes)): ?>
      <tr><td colspan="<?= $_SESSION['user_role'] === 'admin' ? 8 : 7 ?>" style="text-align:center; color:var(--text-muted); padding:40px;">Aucune vente enregistree.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</main></body></html>
