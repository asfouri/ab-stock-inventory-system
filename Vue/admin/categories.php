<?php $pageTitle = 'Categories'; include 'Vue/partials/layout.php'; ?>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <h2 class="page-title">Categories</h2>
    <p class="page-subtitle"><?= count($categories) ?> categorie(s)</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('addModal')">Ajouter</button>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Nom</th><th>Description</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $c): ?>
      <tr>
        <td><strong><?= htmlspecialchars($c['nom']) ?></strong></td>
        <td style="color:var(--text-muted);"><?= htmlspecialchars($c['description'] ?? '-') ?></td>
        <td>
          <div style="display:flex; gap:8px;">
            <button class="btn btn-sm btn-secondary" onclick='editCat(<?= json_encode($c) ?>)'>Modifier</button>
            <form method="POST" action="index.php?page=categories" onsubmit="return confirm('Supprimer ?')">
              <?= csrf_input() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <h3 class="modal-title">Ajouter une categorie</h3>
      <button class="modal-close" onclick="closeModal('addModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=categories">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="create">
      <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
      <div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Creer</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editModal">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header">
      <h3 class="modal-title">Modifier la categorie</h3>
      <button class="modal-close" onclick="closeModal('editModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=categories">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="ec_id">
      <div class="form-group"><label>Nom *</label><input type="text" name="nom" id="ec_nom" required></div>
      <div class="form-group"><label>Description</label><textarea name="description" id="ec_desc"></textarea></div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Mettre a jour</button>
      </div>
    </form>
  </div>
</div>
<script>
function editCat(c) {
  document.getElementById('ec_id').value = c.id;
  document.getElementById('ec_nom').value = c.nom;
  document.getElementById('ec_desc').value = c.description || '';
  openModal('editModal');
}
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.modal-overlay').forEach((m) =>
  m.addEventListener('click', (e) => { if (e.target === m) m.classList.remove('active'); })
);
</script>
</main></body></html>
