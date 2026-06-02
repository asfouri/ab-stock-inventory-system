<?php $pageTitle = 'Caisse'; include 'Vue/partials/layout.php'; ?>

<div class="page-header">
  <h2 class="page-title">Interface de caisse</h2>
  <p class="page-subtitle">Selectionnez un produit pour l'ajouter au panier</p>
</div>

<div class="caisse-layout">
  <div class="caisse-catalogue">
    <div class="toolbar" style="margin-bottom:16px;">
      <div class="search-bar" style="flex:1;">
        <span>Recherche</span>
        <input type="text" id="searchInput" placeholder="Chercher un produit...">
      </div>
      <select id="filterCategorie">
        <option value="">Toutes categories</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="produits-grid" id="prodGrid">
      <?php foreach ($produits as $p): ?>
      <div class="produit-card <?= $p['quantite_stock'] == 0 ? 'rupture' : '' ?>"
           data-id="<?= $p['id'] ?>"
           data-nom="<?= htmlspecialchars($p['nom'], ENT_QUOTES) ?>"
           data-prix="<?= $p['prix_vente'] ?>"
           data-stock="<?= $p['quantite_stock'] ?>"
           data-seuil="<?= $p['seuil_alerte'] ?>"
           data-cat="<?= $p['categorie_id'] ?>"
           onclick="addToCart(this)">
        <div class="prod-name"><?= htmlspecialchars($p['nom']) ?></div>
        <div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:6px;">
          <?= htmlspecialchars($p['categorie_nom'] ?? '') ?>
        </div>
        <div class="prod-price"><?= number_format($p['prix_vente'], 2, ',', ' ') ?> &euro;</div>
        <div class="prod-stock">
          <?php if ($p['quantite_stock'] == 0): ?>
            <span style="color:var(--danger);">Rupture de stock</span>
          <?php elseif ($p['quantite_stock'] <= $p['seuil_alerte']): ?>
            <span style="color:var(--warning);">Stock faible: <?= $p['quantite_stock'] ?></span>
          <?php else: ?>
            <span>Stock: <?= $p['quantite_stock'] ?></span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="caisse-panier">
    <div class="panier-header">Panier <span id="nbItems" style="color:var(--text-muted); font-weight:500; font-size:0.9rem;">(0 article)</span></div>

    <div class="panier-items" id="panierItems">
      <p style="color:var(--text-muted); text-align:center; padding:40px 20px; font-size:0.92rem;">
        Le panier est vide.<br>Ajoutez des produits depuis le catalogue.
      </p>
    </div>

    <div class="panier-footer">
      <div class="total-row">
        <span>Sous-total HT</span>
        <span id="totalHt">0,00 &euro;</span>
      </div>
      <div class="total-row">
        <span>TVA (<?= app_tva() ?>%)</span>
        <span id="totalTva">0,00 &euro;</span>
      </div>
      <div class="total-row ttc">
        <span>Total TTC</span>
        <span class="val" id="totalTtc">0,00 &euro;</span>
      </div>
      <button class="btn btn-primary" style="width:100%; justify-content:center;" onclick="validerVente()">
        Valider la vente
      </button>
      <button class="btn btn-secondary" style="width:100%; justify-content:center; margin-top:8px;" onclick="clearCart()">
        Vider le panier
      </button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="recuModal">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title">Vente validee</h3>
      <button class="modal-close" onclick="closeModal('recuModal')">X</button>
    </div>
    <div id="recuContent"></div>
    <div class="form-actions" style="margin-top:16px;">
      <button class="btn btn-secondary" onclick="window.print()">Imprimer</button>
      <button class="btn btn-primary" onclick="closeModal('recuModal'); clearCart()">Nouvelle vente</button>
    </div>
  </div>
</div>

<script>
const TVA = <?= json_encode(app_tva()) ?>;
const CSRF_TOKEN = <?= json_encode(csrf_token()) ?>;
let panier = {};

document.getElementById('searchInput').addEventListener('input', filterProduits);
document.getElementById('filterCategorie').addEventListener('change', filterProduits);

function filterProduits() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  const cat = document.getElementById('filterCategorie').value;
  document.querySelectorAll('.produit-card').forEach((card) => {
    const matchNom = card.dataset.nom.toLowerCase().includes(q);
    const matchCat = !cat || card.dataset.cat === cat;
    card.style.display = (matchNom && matchCat) ? '' : 'none';
  });
}

function addToCart(card) {
  if (card.classList.contains('rupture')) return;
  const id = card.dataset.id;
  const nom = card.dataset.nom;
  const prix = parseFloat(card.dataset.prix);
  const stock = parseInt(card.dataset.stock, 10);

  if (!panier[id]) {
    panier[id] = { nom, prix, quantite: 0, stock };
  }

  if (panier[id].quantite >= stock) {
    showToast('Stock insuffisant.', 'warning');
    return;
  }

  panier[id].quantite++;
  renderPanier();
}

function renderPanier() {
  const container = document.getElementById('panierItems');
  const ids = Object.keys(panier).filter((k) => panier[k].quantite > 0);

  document.getElementById('nbItems').textContent = `(${ids.length} article${ids.length > 1 ? 's' : ''})`;

  if (ids.length === 0) {
    container.innerHTML = '<p style="color:var(--text-muted); text-align:center; padding:40px 20px; font-size:0.92rem;">Le panier est vide.<br>Ajoutez des produits depuis le catalogue.</p>';
    updateTotals();
    return;
  }

  container.innerHTML = ids.map((id) => {
    const item = panier[id];
    const sub = item.prix * item.quantite;
    return `
    <div class="panier-item" id="item-${id}">
      <div class="panier-item-name">${item.nom}</div>
      <div class="panier-item-qty">
        <button class="qty-btn" onclick="changeQty('${id}', -1)">-</button>
        <input class="qty-input" type="number" value="${item.quantite}" min="1" max="${item.stock}"
               onchange="setQty('${id}', this.value)">
        <button class="qty-btn" onclick="changeQty('${id}', 1)">+</button>
      </div>
      <div class="panier-item-price">${sub.toFixed(2).replace('.', ',')} &euro;</div>
      <button class="remove-item" onclick="removeItem('${id}')">X</button>
    </div>`;
  }).join('');

  updateTotals();
}

function changeQty(id, delta) {
  panier[id].quantite = Math.max(0, Math.min(panier[id].stock, panier[id].quantite + delta));
  if (panier[id].quantite === 0) delete panier[id];
  renderPanier();
}

function setQty(id, val) {
  const qty = parseInt(val, 10);
  if (isNaN(qty) || qty < 1) {
    delete panier[id];
  } else {
    panier[id].quantite = Math.min(panier[id].stock, qty);
  }
  renderPanier();
}

function removeItem(id) {
  delete panier[id];
  renderPanier();
}

function clearCart() {
  panier = {};
  renderPanier();
}

function updateTotals() {
  const ht = Object.values(panier).reduce((s, i) => s + i.prix * i.quantite, 0);
  const tva = ht * TVA / 100;
  const ttc = ht + tva;
  document.getElementById('totalHt').textContent = ht.toFixed(2).replace('.', ',') + ' EUR';
  document.getElementById('totalTva').textContent = tva.toFixed(2).replace('.', ',') + ' EUR';
  document.getElementById('totalTtc').textContent = ttc.toFixed(2).replace('.', ',') + ' EUR';
}

async function validerVente() {
  const items = Object.keys(panier).filter((k) => panier[k].quantite > 0);
  if (items.length === 0) {
    showToast('Le panier est vide.', 'warning');
    return;
  }

  const payload = items.map((id) => ({
    produit_id: parseInt(id, 10),
    quantite: panier[id].quantite
  }));

  try {
    const res = await fetch('index.php?page=valider_vente', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': CSRF_TOKEN
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (data.success) {
      showReceipt(data);
    } else {
      showToast(data.message || 'Erreur lors de la validation.', 'error');
    }
  } catch (e) {
    showToast('Erreur reseau.', 'error');
  }
}

function showReceipt(data) {
  const now = new Date();
  const ht = Object.values(panier).reduce((s, i) => s + i.prix * i.quantite, 0);
  const tva = ht * TVA / 100;
  const lignes = Object.values(panier).map((i) =>
    `<div class="receipt-row"><span>${i.nom} x${i.quantite}</span><span>${(i.prix * i.quantite).toFixed(2)} EUR</span></div>`
  ).join('');

  document.getElementById('recuContent').innerHTML = `
    <div class="receipt">
      <h2><?= htmlspecialchars(app_name()) ?></h2>
      <div style="text-align:center; font-size:0.8rem; margin-bottom:12px; color:#64748b;">
        ${now.toLocaleDateString('fr-FR')} ${now.toLocaleTimeString('fr-FR')}<br>
        Reference : <strong>${data.reference}</strong>
      </div>
      ${lignes}
      <div class="receipt-row" style="margin-top:8px; padding-top:4px; border-top:1px solid #dbe3ec;">
        <span>Sous-total HT</span><span>${ht.toFixed(2)} EUR</span>
      </div>
      <div class="receipt-row">
        <span>TVA ${TVA}%</span><span>${tva.toFixed(2)} EUR</span>
      </div>
      <div class="receipt-row receipt-total">
        <span>Total TTC</span><span>${data.total_ttc.toFixed(2)} EUR</span>
      </div>
      <div style="text-align:center; margin-top:16px; font-size:0.75rem; color:#94a3b8;">
        Merci de votre visite.
      </div>
    </div>`;
  openModal('recuModal');

  Object.keys(panier).forEach((id) => {
    const card = document.querySelector(`.produit-card[data-id="${id}"]`);
    if (card) {
      const newStock = parseInt(card.dataset.stock, 10) - panier[id].quantite;
      const seuil = parseInt(card.dataset.seuil, 10);
      card.dataset.stock = Math.max(0, newStock);
      const stockEl = card.querySelector('.prod-stock');
      if (newStock <= 0) {
        card.classList.add('rupture');
        stockEl.innerHTML = '<span style="color:var(--danger);">Rupture de stock</span>';
      } else if (newStock <= seuil) {
        stockEl.innerHTML = `<span style="color:var(--warning);">Stock faible: ${newStock}</span>`;
      } else {
        stockEl.innerHTML = `<span>Stock: ${newStock}</span>`;
      }
    }
  });
}

function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  t.className = `alert alert-${type}`;
  t.style.cssText = 'position:fixed; top:24px; right:24px; z-index:9999; min-width:280px;';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3000);
}

function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.modal-overlay').forEach((m) =>
  m.addEventListener('click', (e) => { if (e.target === m) m.classList.remove('active'); })
);
</script>

</main></body></html>
