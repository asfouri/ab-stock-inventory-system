<?php $pageTitle = 'Utilisateurs'; include 'Vue/partials/layout.php'; ?>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <h2 class="page-title">Gestion des utilisateurs</h2>
    <p class="page-subtitle"><?= count($utilisateurs) ?> utilisateur(s)</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('addModal')">Ajouter un utilisateur</button>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Nom complet</th>
        <th>Email</th>
        <th>Role</th>
        <th>Fournisseur lie</th>
        <th>Statut</th>
        <th>Date creation</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($utilisateurs as $u): ?>
      <tr>
        <td><strong><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></strong></td>
        <td style="color:var(--text-muted);"><?= htmlspecialchars($u['email']) ?></td>
        <td>
          <?php $roles = ['admin' => 'badge-danger', 'vendeur' => 'badge-info', 'fournisseur' => 'badge-warning']; ?>
          <span class="badge <?= $roles[$u['role']] ?? 'badge-muted' ?>">
            <?= ucfirst($u['role']) ?>
          </span>
        </td>
        <td><?= htmlspecialchars($u['fournisseur_nom'] ?? '-') ?></td>
        <td>
          <span class="badge <?= $u['actif'] ? 'badge-success' : 'badge-muted' ?>">
            <?= $u['actif'] ? 'Actif' : 'Desactive' ?>
          </span>
        </td>
        <td style="color:var(--text-muted);"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
        <td>
          <div style="display:flex; gap:8px; align-items:center;">
            <?php if ($u['id'] != $_SESSION['user_id']): ?>
            <button class="btn btn-sm btn-secondary" onclick='editUser(<?= json_encode($u) ?>)'>Modifier</button>
            <form method="POST" action="index.php?page=utilisateurs" onsubmit="return confirm('Desactiver cet utilisateur ?')">
              <?= csrf_input() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $u['id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger">Desactiver</button>
            </form>
            <?php else: ?>
            <span style="font-size:0.78rem; color:var(--text-muted);">Compte actuel</span>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Ajouter un utilisateur</h3>
      <button class="modal-close" onclick="closeModal('addModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=utilisateurs">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="create">
      <div class="form-row">
        <div class="form-group">
          <label>Prenom *</label>
          <input type="text" name="prenom" required>
        </div>
        <div class="form-group">
          <label>Nom *</label>
          <input type="text" name="nom" required>
        </div>
      </div>
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" required>
      </div>
      <div class="form-group">
        <label>Mot de passe *</label>
        <input type="password" name="mot_de_passe" required minlength="6">
      </div>
      <div class="form-group">
        <label>Role *</label>
        <select name="role" required>
          <option value="vendeur">Vendeur</option>
          <option value="admin">Administrateur</option>
          <option value="fournisseur">Fournisseur</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Creer</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Modifier l'utilisateur</h3>
      <button class="modal-close" onclick="closeModal('editModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=utilisateurs">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-row">
        <div class="form-group">
          <label>Prenom *</label>
          <input type="text" name="prenom" id="eu_prenom" required>
        </div>
        <div class="form-group">
          <label>Nom *</label>
          <input type="text" name="nom" id="eu_nom" required>
        </div>
      </div>
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" id="eu_email" required>
      </div>
      <div class="form-group">
        <label>Nouveau mot de passe</label>
        <input type="password" name="mot_de_passe" minlength="6">
      </div>
      <div class="form-group">
        <label>Role *</label>
        <select name="role" id="eu_role" required>
          <option value="vendeur">Vendeur</option>
          <option value="admin">Administrateur</option>
          <option value="fournisseur">Fournisseur</option>
        </select>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Mettre a jour</button>
      </div>
    </form>
  </div>
</div>

<script>
function editUser(u) {
  document.getElementById('edit_id').value = u.id;
  document.getElementById('eu_prenom').value = u.prenom;
  document.getElementById('eu_nom').value = u.nom;
  document.getElementById('eu_email').value = u.email;
  document.getElementById('eu_role').value = u.role;
  openModal('editModal');
}
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.modal-overlay').forEach((m) =>
  m.addEventListener('click', (e) => { if (e.target === m) m.classList.remove('active'); })
);
</script>

</main></body></html>
