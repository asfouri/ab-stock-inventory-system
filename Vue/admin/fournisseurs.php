<?php $pageTitle = 'Fournisseurs'; include 'Vue/partials/layout.php'; ?>

<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <h2 class="page-title">Fournisseurs</h2>
    <p class="page-subtitle"><?= count($fournisseurs) ?> fournisseur(s)</p>
  </div>
  <div style="display:flex; gap:10px; flex-wrap:wrap;">
    <form method="POST" action="index.php?page=fournisseurs">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="sync_accounts">
      <button type="submit" class="btn btn-secondary">Creer les comptes manquants</button>
    </form>
    <button class="btn btn-primary" onclick="openModal('addModal')">Ajouter</button>
  </div>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Nom</th><th>Contact</th><th>Email</th><th>Telephone</th><th>Adresse</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($fournisseurs as $f): ?>
      <tr>
        <td><strong><?= htmlspecialchars($f['nom']) ?></strong></td>
        <td><?= htmlspecialchars($f['contact'] ?? '-') ?></td>
        <td style="color:var(--accent-strong);"><?= htmlspecialchars($f['email'] ?? '-') ?></td>
        <td><?= htmlspecialchars($f['telephone'] ?? '-') ?></td>
        <td style="color:var(--text-muted); max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($f['adresse'] ?? '-') ?></td>
        <td>
          <div style="display:flex; gap:8px;">
            <button class="btn btn-sm btn-secondary" onclick='editFour(<?= json_encode($f) ?>)'>Modifier</button>
            <form method="POST" action="index.php?page=fournisseurs" onsubmit="return confirm('Supprimer ?')">
              <?= csrf_input() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $f['id'] ?>">
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
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Ajouter un fournisseur</h3>
      <button class="modal-close" onclick="closeModal('addModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=fournisseurs">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="create">
      <div class="form-row">
        <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
        <div class="form-group"><label>Contact</label><input type="text" name="contact"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Email</label><input type="email" name="email"></div>
        <div class="form-group"><label>Telephone</label><input type="text" name="telephone"></div>
      </div>
      <div class="form-group"><label>Adresse</label><textarea name="adresse"></textarea></div>
      <div class="form-group">
        <label>Compte de connexion</label>
        <select name="account_mode">
          <option value="create">Creer un compte fournisseur</option>
          <option value="link_existing">Lier un utilisateur existant</option>
          <option value="none">Ne pas creer de compte</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Mot de passe de connexion</label><input type="password" name="user_password" placeholder="Requis si creation de compte"></div>
        <div class="form-group"><label>ID utilisateur a lier</label><input type="number" name="utilisateur_id" placeholder="Utilise seulement avec l'option lier"></div>
      </div>
      <p style="font-size:0.82rem; color:var(--text-muted); margin-top:-6px;">Pour creer un compte de connexion, renseignez l'email du fournisseur et un mot de passe.</p>
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
      <h3 class="modal-title">Modifier le fournisseur</h3>
      <button class="modal-close" onclick="closeModal('editModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=fournisseurs">
      <?= csrf_input() ?>
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="ef_id">
      <div class="form-row">
        <div class="form-group"><label>Nom *</label><input type="text" name="nom" id="ef_nom" required></div>
        <div class="form-group"><label>Contact</label><input type="text" name="contact" id="ef_contact"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Email</label><input type="email" name="email" id="ef_email"></div>
        <div class="form-group"><label>Telephone</label><input type="text" name="telephone" id="ef_telephone"></div>
      </div>
      <div class="form-group"><label>Adresse</label><textarea name="adresse" id="ef_adresse"></textarea></div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Mettre a jour</button>
      </div>
    </form>
  </div>
</div>

<script>
function editFour(f) {
  document.getElementById('ef_id').value = f.id;
  document.getElementById('ef_nom').value = f.nom;
  document.getElementById('ef_contact').value = f.contact || '';
  document.getElementById('ef_email').value = f.email || '';
  document.getElementById('ef_telephone').value = f.telephone || '';
  document.getElementById('ef_adresse').value = f.adresse || '';
  openModal('editModal');
}
function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.modal-overlay').forEach((m) =>
  m.addEventListener('click', (e) => { if (e.target === m) m.classList.remove('active'); })
);
</script>
</main></body></html>
