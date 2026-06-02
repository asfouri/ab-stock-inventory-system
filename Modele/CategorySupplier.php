<?php
// Modele/Category.php

class Category extends BaseModel {

    public function getAll(): array {
        return $this->db->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO categories (nom, description) VALUES (?,?)");
        return $stmt->execute([$data['nom'], $data['description']]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE categories SET nom=?, description=? WHERE id=?");
        return $stmt->execute([$data['nom'], $data['description'], $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id=?");
        return $stmt->execute([$id]);
    }
}

// Modele/Supplier.php

class Supplier extends BaseModel {

    public function getAll(): array {
        return $this->db->query(
            "SELECT f.*, u.email AS user_email FROM fournisseurs f LEFT JOIN utilisateurs u ON u.id=f.utilisateur_id ORDER BY f.nom"
        )->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM fournisseurs WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM fournisseurs WHERE utilisateur_id=? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM fournisseurs WHERE email=? ORDER BY utilisateur_id IS NULL, id LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function getWithoutUser(): array {
        return $this->db->query(
            "SELECT * FROM fournisseurs WHERE (utilisateur_id IS NULL OR utilisateur_id = 0) AND email IS NOT NULL AND email <> '' ORDER BY id"
        )->fetchAll();
    }

    public function linkUser(int $supplierId, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE fournisseurs SET utilisateur_id=? WHERE id=?");
        return $stmt->execute([$userId, $supplierId]);
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO fournisseurs (nom, contact, email, telephone, adresse, utilisateur_id) VALUES (?,?,?,?,?,?)"
        );
        return $stmt->execute([
            $data['nom'], $data['contact'], $data['email'],
            $data['telephone'], $data['adresse'], $data['utilisateur_id'] ?: null
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE fournisseurs SET nom=?, contact=?, email=?, telephone=?, adresse=? WHERE id=?"
        );
        return $stmt->execute([$data['nom'], $data['contact'], $data['email'], $data['telephone'], $data['adresse'], $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM fournisseurs WHERE id=?");
        return $stmt->execute([$id]);
    }
}
