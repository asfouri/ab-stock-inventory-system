<?php
// Modele/Product.php

class Product extends BaseModel {

    public function getAll(array $filters = []): array {
        $sql = "SELECT p.*, c.nom AS categorie_nom, f.nom AS fournisseur_nom
                FROM produits p
                LEFT JOIN categories c ON c.id = p.categorie_id
                LEFT JOIN fournisseurs f ON f.id = p.fournisseur_id
                WHERE p.actif = 1";
        $params = [];

        if (!empty($filters['categorie_id'])) {
            $sql .= " AND p.categorie_id = ?";
            $params[] = $filters['categorie_id'];
        }
        if (!empty($filters['fournisseur_id'])) {
            $sql .= " AND p.fournisseur_id = ?";
            $params[] = $filters['fournisseur_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (p.nom LIKE ? OR p.description LIKE ? OR p.code_barre LIKE ?)";
            $term = '%' . $filters['search'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        if (!empty($filters['fournisseur_user_id'])) {
            $sql .= " AND p.fournisseur_id = (SELECT id FROM fournisseurs WHERE utilisateur_id = ?)";
            $params[] = $filters['fournisseur_user_id'];
        }

        $sql .= " ORDER BY p.nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.nom AS categorie_nom, f.nom AS fournisseur_nom
             FROM produits p
             LEFT JOIN categories c ON c.id = p.categorie_id
             LEFT JOIN fournisseurs f ON f.id = p.fournisseur_id
             WHERE p.id = ? AND p.actif = 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO produits (nom, description, prix_achat, prix_vente, quantite_stock, seuil_alerte, categorie_id, fournisseur_id, code_barre)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['nom'],
            $data['description'],
            $data['prix_achat'],
            $data['prix_vente'],
            $data['quantite_stock'],
            $data['seuil_alerte'],
            $data['categorie_id'] ?: null,
            $data['fournisseur_id'] ?: null,
            $data['code_barre'] ?: null,
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE produits
             SET nom=?, description=?, prix_achat=?, prix_vente=?, quantite_stock=?, seuil_alerte=?, categorie_id=?, fournisseur_id=?, code_barre=?
             WHERE id=?"
        );
        return $stmt->execute([
            $data['nom'],
            $data['description'],
            $data['prix_achat'],
            $data['prix_vente'],
            $data['quantite_stock'],
            $data['seuil_alerte'],
            $data['categorie_id'] ?: null,
            $data['fournisseur_id'] ?: null,
            $data['code_barre'] ?: null,
            $id,
        ]);
    }

    public function delete(int $id): array {
        $refStmt = $this->db->prepare("SELECT COUNT(*) FROM lignes_vente WHERE produit_id = ?");
        $refStmt->execute([$id]);
        $references = (int) $refStmt->fetchColumn();

        if ($references > 0) {
            return [
                'success' => false,
                'reason' => 'referenced',
            ];
        }

        $stmt = $this->db->prepare("DELETE FROM produits WHERE id=?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() < 1) {
            return [
                'success' => false,
                'reason' => 'not_found',
            ];
        }

        return [
            'success' => true,
            'reason' => 'deleted',
        ];
    }

    public function decrementStock(int $id, int $qty): bool {
        $stmt = $this->db->prepare(
            "UPDATE produits SET quantite_stock = quantite_stock - ? WHERE id = ? AND quantite_stock >= ?"
        );
        $stmt->execute([$qty, $id, $qty]);
        return $stmt->rowCount() > 0;
    }

    public function getLowStock(): array {
        $stmt = $this->db->query(
            "SELECT p.*, c.nom AS categorie_nom
             FROM produits p
             LEFT JOIN categories c ON c.id = p.categorie_id
             WHERE p.actif = 1 AND p.quantite_stock <= p.seuil_alerte
             ORDER BY p.quantite_stock ASC"
        );
        return $stmt->fetchAll();
    }

    public function count(): int {
        return (int) $this->db->query("SELECT COUNT(*) FROM produits WHERE actif = 1")->fetchColumn();
    }

    public function getTotalStockValue(): float {
        return (float) $this->db->query("SELECT SUM(prix_achat * quantite_stock) FROM produits WHERE actif = 1")->fetchColumn();
    }
}
