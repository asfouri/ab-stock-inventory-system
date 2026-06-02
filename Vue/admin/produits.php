<?php $pageTitle = 'Produits'; include 'Vue/partials/layout.php'; ?>



<div class="page-header" style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
  <div>
    <h2 class="page-title">Catalogue produits</h2>
    <p class="page-subtitle">
      <?= count($produits) ?> produit(s) affiche(s)
      <?php if (($totalProduits ?? count($produits)) !== count($produits)): ?>
        sur <?= (int) $totalProduits ?> trouve(s)
      <?php endif; ?>
    </p>
  </div>
  <?php if ($_SESSION['user_role'] === 'admin'): ?>
  <button class="btn btn-primary" onclick="openModal('addModal')">Ajouter un produit</button>
  <?php endif; ?>
</div>

<?php if (!empty($lowStock) && $_SESSION['user_role'] !== 'fournisseur'): ?>
<div class="alert-stock-banner">
  <span class="icon">AL</span>
  <strong><?= count($lowStock) ?> produit(s) sous le seuil d'alerte</strong>
  <span style="font-size:0.85rem; color:var(--text-muted);">
    <?= htmlspecialchars(implode(', ', array_slice(array_column($lowStock, 'nom'), 0, 4))) ?>
    <?= count($lowStock) > 4 ? '...' : '' ?>
  </span>
</div>
<?php endif; ?>

<form method="GET" action="index.php">
  <input type="hidden" name="page" value="produits">
  <div class="toolbar">
    <div class="search-bar" style="flex:2;">
      <span>Recherche</span>
      <input type="text" id="productSearch" name="search" placeholder="Rechercher un produit..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </div>
    <select id="productCategoryFilter" name="categorie_id">
      <option value="">Toutes les categories</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>" <?= ($_GET['categorie_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($c['nom']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php if ($_SESSION['user_role'] !== 'fournisseur'): ?>
    <select id="productSupplierFilter" name="fournisseur_id">
      <option value="">Tous les fournisseurs</option>
      <?php foreach ($fournisseurs as $f): ?>
        <option value="<?= $f['id'] ?>" <?= ($_GET['fournisseur_id'] ?? '') == $f['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($f['nom']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <select name="limit" aria-label="Nombre de produits a afficher">
       <option value="3" <?= ($selectedLimit ?? '10') === '3' ? 'selected' : '' ?>>3</option>
      <option value="5" <?= ($selectedLimit ?? '10') === '5' ? 'selected' : '' ?>>5 produits</option>
      <option value="10" <?= ($selectedLimit ?? '10') === '10' ? 'selected' : '' ?>>10 produits</option>
      <option value="20" <?= ($selectedLimit ?? '10') === '20' ? 'selected' : '' ?>>20 produits</option>
      <option value="50" <?= ($selectedLimit ?? '10') === '50' ? 'selected' : '' ?>>50 produits</option>
      <option value="100" <?= ($selectedLimit ?? '10') === '100' ? 'selected' : '' ?>>100 produits</option>
      <option value="all" <?= ($selectedLimit ?? '10') === 'all' ? 'selected' : '' ?>>Tous</option>
    </select>
    <button type="submit" class="btn btn-secondary">Filtrer</button>
    <a href="index.php?page=produits" class="btn btn-secondary">Reinitialiser</a>
  </div>
</form>

<div class="table-wrap">
  <table>
    <thead>
      <tr>
        <th>Produit</th>
        <th>Categorie</th>
        <th>Fournisseur</th>
        <th>Prix achat</th>
        <th>Prix vente</th>
        <th>Stock</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produits as $p):
        $ratio = $p['seuil_alerte'] > 0 ? min(100, ($p['quantite_stock'] / ($p['seuil_alerte'] * 3)) * 100) : 100;
        $stockClass = $p['quantite_stock'] == 0 ? 'stock-critical' : ($p['quantite_stock'] <= $p['seuil_alerte'] ? 'stock-low' : 'stock-ok');
      ?>
      <tr data-name="<?= htmlspecialchars(function_exists('mb_strtolower') ? mb_strtolower($p['nom']) : strtolower($p['nom']), ENT_QUOTES) ?>"
          data-category="<?= (int)($p['categorie_id'] ?? 0) ?>"
          data-supplier="<?= (int)($p['fournisseur_id'] ?? 0) ?>">
        <td>
          <div style="font-weight:700; color:var(--text);"><?= htmlspecialchars($p['nom']) ?></div>
          <?php if ($p['code_barre']): ?>
            <div style="font-size:0.72rem; color:var(--text-muted);">Code: <?= htmlspecialchars($p['code_barre']) ?></div>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['categorie_nom'] ?? '-') ?></td>
        <td><?= htmlspecialchars($p['fournisseur_nom'] ?? '-') ?></td>
        <td><?= number_format($p['prix_achat'], 2, ',', ' ') ?> &euro;</td>
        <td style="font-weight:700; color:var(--accent-strong);"><?= number_format($p['prix_vente'], 2, ',', ' ') ?> &euro;</td>
        <td>
          <div class="stock-indicator <?= $stockClass ?>">
            <strong style="min-width:30px;"><?= $p['quantite_stock'] ?></strong>
            <div class="stock-bar">
              <div class="stock-fill" style="width:<?= min(100, $ratio) ?>%"></div>
            </div>
            <span style="font-size:0.72rem; color:var(--text-muted);">/ <?= $p['seuil_alerte'] ?></span>
          </div>
        </td>
        <td>
          <?php if ($p['quantite_stock'] == 0): ?>
            <span class="badge badge-danger">Rupture</span>
          <?php elseif ($p['quantite_stock'] <= $p['seuil_alerte']): ?>
            <span class="badge badge-warning">Stock faible</span>
          <?php else: ?>
            <span class="badge badge-success">Disponible</span>
          <?php endif; ?>
        </td>
        <td>
          <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn btn-sm btn-secondary" onclick='showProductDetails(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>Afficher</button>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <button type="button" class="btn btn-sm btn-secondary" onclick='editProduit(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>Modifier</button>
            <form method="POST" action="index.php?page=produits&action=delete" onsubmit="return confirm('Supprimer ce produit ?')">
              <?= csrf_input() ?>
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
            </form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($produits)): ?>
      <tr><td colspan="8" style="text-align:center; color:var(--text-muted); padding:40px;">Aucun produit trouve.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="modal-overlay" id="productDetailModal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <h3 class="modal-title">Informations du produit</h3>
      <button type="button" class="modal-close" onclick="closeModal('productDetailModal')">X</button>
    </div>

    <div class="product-detail-hero">
      <div class="product-detail-badge" id="detail_initials">PR</div>
      <div class="product-detail-copy">
        <div class="product-detail-status" id="detail_status"></div>
        <h3 id="detail_nom">Produit</h3>
        <p id="detail_category">Categorie</p>
      </div>
    </div>

    <div class="product-detail-grid">
      <div class="product-detail-card">
        <span class="product-detail-label">Code barre</span>
        <strong id="detail_code">-</strong>
      </div>
      <div class="product-detail-card">
        <span class="product-detail-label">Fournisseur</span>
        <strong id="detail_supplier">-</strong>
      </div>
      <div class="product-detail-card">
        <span class="product-detail-label">Prix d'achat</span>
        <strong id="detail_buy_price">-</strong>
      </div>
      <div class="product-detail-card">
        <span class="product-detail-label">Prix de vente</span>
        <strong id="detail_sell_price">-</strong>
      </div>
      <div class="product-detail-card">
        <span class="product-detail-label">Marge</span>
        <strong id="detail_margin">-</strong>
      </div>
      <div class="product-detail-card">
        <span class="product-detail-label">Seuil d'alerte</span>
        <strong id="detail_threshold">-</strong>
      </div>
    </div>

    <div class="product-detail-stock-box">
      <div class="stock-indicator" id="detail_stock_indicator">
        <strong id="detail_stock_value">0</strong>
        <div class="stock-bar">
          <div class="stock-fill" id="detail_stock_fill" style="width:0%"></div>
        </div>
        <span id="detail_stock_text">Seuil 0</span>
      </div>
    </div>

    <div class="product-detail-description-box">
      <span class="product-detail-label">Description</span>
      <p id="detail_description">Aucune description disponible.</p>
    </div>
  </div>
</div>

<?php if ($_SESSION['user_role'] === 'admin'): ?>
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Ajouter un produit</h3>
      <button class="modal-close" onclick="closeModal('addModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=produits&action=store">
      <?= csrf_input() ?>
      <div class="form-row">
        <div class="form-group">
          <label>Nom du produit *</label>
          <input type="text" name="nom" required>
        </div>
        <div class="form-group">
          <label>Code barre</label>
          <input type="text" name="code_barre">
        </div>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description"></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Prix d'achat (&euro;) *</label>
          <input type="number" name="prix_achat" step="0.01" min="0" required>
        </div>
        <div class="form-group">
          <label>Prix de vente (&euro;) *</label>
          <input type="number" name="prix_vente" step="0.01" min="0" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Quantite en stock *</label>
          <input type="number" name="quantite_stock" min="0" required>
        </div>
        <div class="form-group">
          <label>Seuil d'alerte *</label>
          <input type="number" name="seuil_alerte" min="0" value="5" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Categorie</label>
          <select name="categorie_id">
            <option value="">Aucune</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Fournisseur</label>
          <select name="fournisseur_id">
            <option value="">Aucun</option>
            <?php foreach ($fournisseurs as $f): ?>
              <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Modifier le produit</h3>
      <button class="modal-close" onclick="closeModal('editModal')">X</button>
    </div>
    <form method="POST" action="index.php?page=produits&action=update" id="editForm">
      <?= csrf_input() ?>
      <input type="hidden" name="id" id="edit_id">
      <div class="form-row">
        <div class="form-group">
          <label>Nom du produit *</label>
          <input type="text" name="nom" id="edit_nom" required>
        </div>
        <div class="form-group">
          <label>Code barre</label>
          <input type="text" name="code_barre" id="edit_code_barre">
        </div>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" id="edit_description"></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Prix d'achat (&euro;) *</label>
          <input type="number" name="prix_achat" id="edit_prix_achat" step="0.01" min="0" required>
        </div>
        <div class="form-group">
          <label>Prix de vente (&euro;) *</label>
          <input type="number" name="prix_vente" id="edit_prix_vente" step="0.01" min="0" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Quantite en stock *</label>
          <input type="number" name="quantite_stock" id="edit_quantite_stock" min="0" required>
        </div>
        <div class="form-group">
          <label>Seuil d'alerte *</label>
          <input type="number" name="seuil_alerte" id="edit_seuil_alerte" min="0" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Categorie</label>
          <select name="categorie_id" id="edit_categorie_id">
            <option value="">Aucune</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Fournisseur</label>
          <select name="fournisseur_id" id="edit_fournisseur_id">
            <option value="">Aucun</option>
            <?php foreach ($fournisseurs as $f): ?>
              <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Annuler</button>
        <button type="submit" class="btn btn-primary">Mettre a jour</button>
      </div>
    </form>
  </div>
</div>

<script>
function editProduit(p) {
  document.getElementById('edit_id').value = p.id;
  document.getElementById('edit_nom').value = p.nom;
  document.getElementById('edit_code_barre').value = p.code_barre || '';
  document.getElementById('edit_description').value = p.description || '';
  document.getElementById('edit_prix_achat').value = p.prix_achat;
  document.getElementById('edit_prix_vente').value = p.prix_vente;
  document.getElementById('edit_quantite_stock').value = p.quantite_stock;
  document.getElementById('edit_seuil_alerte').value = p.seuil_alerte;
  document.getElementById('edit_categorie_id').value = p.categorie_id || '';
  document.getElementById('edit_fournisseur_id').value = p.fournisseur_id || '';
  openModal('editModal');
}
</script>
<?php endif; ?>

<script>
function formatPrice(value) {
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(Number(value || 0));
}

function productStatusBadge(product) {
  const stock = Number(product.quantite_stock || 0);
  const threshold = Number(product.seuil_alerte || 0);
  if (stock === 0) {
    return '<span class="badge badge-danger">Rupture</span>';
  }
  if (stock <= threshold) {
    return '<span class="badge badge-warning">Stock faible</span>';
  }
  return '<span class="badge badge-success">Disponible</span>';
}

function stockClassName(product) {
  const stock = Number(product.quantite_stock || 0);
  const threshold = Number(product.seuil_alerte || 0);
  if (stock === 0) {
    return 'stock-critical';
  }
  if (stock <= threshold) {
    return 'stock-low';
  }
  return 'stock-ok';
}

function productInitials(name) {
  const safeName = String(name || '').trim();
  return (safeName.slice(0, 2) || 'PR').toUpperCase();
}

function showProductDetails(product) {
  const stock = Number(product.quantite_stock || 0);
  const threshold = Number(product.seuil_alerte || 0);
  const ratio = threshold > 0 ? Math.min(100, (stock / (threshold * 3)) * 100) : 100;
  const margin = Number(product.prix_vente || 0) - Number(product.prix_achat || 0);
  const description = String(product.description || '').trim() || 'Aucune description disponible.';
  const indicator = document.getElementById('detail_stock_indicator');

  document.getElementById('detail_initials').textContent = productInitials(product.nom);
  document.getElementById('detail_nom').textContent = product.nom || 'Produit';
  document.getElementById('detail_category').textContent = product.categorie_nom || 'Sans categorie';
  document.getElementById('detail_status').innerHTML = productStatusBadge(product);
  document.getElementById('detail_code').textContent = product.code_barre || 'Non defini';
  document.getElementById('detail_supplier').textContent = product.fournisseur_nom || 'Non assigne';
  document.getElementById('detail_buy_price').textContent = formatPrice(product.prix_achat);
  document.getElementById('detail_sell_price').textContent = formatPrice(product.prix_vente);
  document.getElementById('detail_margin').textContent = formatPrice(margin);
  document.getElementById('detail_threshold').textContent = String(threshold);
  document.getElementById('detail_stock_value').textContent = String(stock);
  document.getElementById('detail_stock_text').textContent = `Seuil ${threshold}`;
  document.getElementById('detail_stock_fill').style.width = `${ratio}%`;
  document.getElementById('detail_description').textContent = description;

  indicator.className = `stock-indicator ${stockClassName(product)}`;
  openModal('productDetailModal');
}

function filterProductsTable() {
  const searchEl = document.getElementById('productSearch');
  const categoryEl = document.getElementById('productCategoryFilter');
  const supplierEl = document.getElementById('productSupplierFilter');
  const query = (searchEl?.value || '').trim().toLowerCase();
  const category = categoryEl?.value || '';
  const supplier = supplierEl?.value || '';

  document.querySelectorAll('tbody tr[data-name]').forEach((row) => {
    const matchSearch = !query || row.dataset.name.includes(query);
    const matchCategory = !category || row.dataset.category === category;
    const matchSupplier = !supplier || row.dataset.supplier === supplier;
    row.style.display = (matchSearch && matchCategory && matchSupplier) ? '' : 'none';
  });
}

document.getElementById('productSearch')?.addEventListener('input', filterProductsTable);
document.getElementById('productCategoryFilter')?.addEventListener('change', filterProductsTable);
document.getElementById('productSupplierFilter')?.addEventListener('change', filterProductsTable);

function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.modal-overlay').forEach((m) =>
  m.addEventListener('click', (e) => { if (e.target === m) m.classList.remove('active'); })
);
</script>
</main></body></html>
