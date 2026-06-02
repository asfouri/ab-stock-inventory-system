<?php
// Modele/SaleManager.php

class SaleManager extends BaseModel {

    public function createSale(int $vendeurId, array $panier): array {
        if (empty($panier)) {
            return ['success' => false, 'message' => 'Le panier est vide.'];
        }

        $this->db->beginTransaction();
        try {
            $totalHt = 0;
            $productModel = new Product();

            foreach ($panier as $item) {
                $prod = $productModel->findById($item['produit_id']);
                if (!$prod) throw new Exception("Produit introuvable (ID: {$item['produit_id']}).");
                if ($prod['quantite_stock'] < $item['quantite']) {
                    throw new Exception("Stock insuffisant pour \"{$prod['nom']}\" (dispo: {$prod['quantite_stock']}).");
                }
                $totalHt += $prod['prix_vente'] * $item['quantite'];
            }

            $tva    = app_tva();
            $ttc    = $totalHt * (1 + $tva / 100);
            $ref    = 'VTE-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $stmt = $this->db->prepare(
                "INSERT INTO ventes (reference, vendeur_id, total_ht, taux_tva, total_ttc, statut) VALUES (?,?,?,?,?,'validee')"
            );
            $stmt->execute([$ref, $vendeurId, round($totalHt, 2), $tva, round($ttc, 2)]);
            $venteId = (int) $this->db->lastInsertId();

            foreach ($panier as $item) {
                $prod = $productModel->findById($item['produit_id']);
                $sousTotal = $prod['prix_vente'] * $item['quantite'];

                $stmt = $this->db->prepare(
                    "INSERT INTO lignes_vente (vente_id, produit_id, quantite, prix_unitaire, sous_total) VALUES (?,?,?,?,?)"
                );
                $stmt->execute([$venteId, $item['produit_id'], $item['quantite'], $prod['prix_vente'], $sousTotal]);

                $productModel->decrementStock($item['produit_id'], $item['quantite']);
            }

            $this->db->commit();
            return ['success' => true, 'vente_id' => $venteId, 'reference' => $ref, 'total_ttc' => round($ttc, 2)];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAll(int $vendeurId = 0): array {
        if ($vendeurId) {
            $stmt = $this->db->prepare(
                "SELECT v.*, CONCAT(u.prenom,' ',u.nom) AS vendeur_nom FROM ventes v
                 JOIN utilisateurs u ON u.id = v.vendeur_id WHERE v.vendeur_id=? ORDER BY v.created_at DESC"
            );
            $stmt->execute([$vendeurId]);
        } else {
            $stmt = $this->db->query(
                "SELECT v.*, CONCAT(u.prenom,' ',u.nom) AS vendeur_nom FROM ventes v
                 JOIN utilisateurs u ON u.id = v.vendeur_id ORDER BY v.created_at DESC"
            );
        }
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT v.*, CONCAT(u.prenom,' ',u.nom) AS vendeur_nom FROM ventes v
             JOIN utilisateurs u ON u.id = v.vendeur_id WHERE v.id=?"
        );
        $stmt->execute([$id]);
        $vente = $stmt->fetch();
        if (!$vente) return null;

        $stmt = $this->db->prepare(
            "SELECT lv.*, p.nom AS produit_nom FROM lignes_vente lv JOIN produits p ON p.id=lv.produit_id WHERE lv.vente_id=?"
        );
        $stmt->execute([$id]);
        $vente['lignes'] = $stmt->fetchAll();
        return $vente;
    }

    public function getChiffreAffaires(): array {
        $row = $this->db->query(
            "SELECT SUM(total_ttc) AS ca_total, COUNT(*) AS nb_ventes FROM ventes WHERE statut='validee'"
        )->fetch();
        return $row;
    }

    public function getChiffreAffairesMois(): array {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS mois, SUM(total_ttc) AS ca, COUNT(*) AS nb
             FROM ventes WHERE statut='validee' GROUP BY mois ORDER BY mois DESC LIMIT 12"
        );
        return $stmt->fetchAll();
    }

    public function getTopProduits(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT p.nom, SUM(lv.quantite) AS total_vendu, SUM(lv.sous_total) AS ca
             FROM lignes_vente lv JOIN produits p ON p.id=lv.produit_id
             JOIN ventes v ON v.id=lv.vente_id WHERE v.statut='validee'
             GROUP BY lv.produit_id ORDER BY total_vendu DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function clearHistory(): int {
        $count = (int) $this->db->query("SELECT COUNT(*) FROM ventes")->fetchColumn();
        if ($count === 0) {
            return 0;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("DELETE FROM ventes");
            $stmt->execute();
            $this->db->commit();
            return $count;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function deleteById(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM ventes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
