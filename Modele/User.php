<?php
// Modele/User.php

class User extends BaseModel {
    private const ROLE_TABLE_MAP = [
        'admin' => 'admins',
        'vendeur' => 'vendeurs',
    ];

    public int $id;
    public string $nom;
    public string $prenom;
    public string $email;
    public string $role;
    public int $actif;

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = ? AND actif = 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findUtilisateurByEmail(string $email): ?Utilisateur {
        $user = $this->findByEmail($email);
        return $user ? Utilisateur::fromArray($user) : null;
    }

    public function findAnyByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findActiveById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ? AND actif = 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findUtilisateurById(int $id): ?Utilisateur {
        $user = $this->findById($id);
        return $user ? Utilisateur::fromArray($user) : null;
    }

    public function hydrateUtilisateur(array $data): Utilisateur {
        return Utilisateur::fromArray($data);
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT u.*, f.nom AS fournisseur_nom FROM utilisateurs u LEFT JOIN fournisseurs f ON f.utilisateur_id = u.id ORDER BY u.created_at DESC");
        return $stmt->fetchAll();
    }

    public function create(array $data): bool {
        return $this->createAndGetId($data) > 0;
    }

    public function createAndGetId(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            password_hash($data['mot_de_passe'], PASSWORD_BCRYPT),
            $data['role']
        ]);
        $userId = (int) $this->db->lastInsertId();
        $this->syncRoleSpecialization($userId, (string) $data['role']);
        return $userId;
    }

    public function update(int $id, array $data): bool {
        if (!empty($data['mot_de_passe'])) {
            $stmt = $this->db->prepare(
                "UPDATE utilisateurs SET nom=?, prenom=?, email=?, role=?, mot_de_passe=? WHERE id=?"
            );
            $ok = $stmt->execute([
                $data['nom'], $data['prenom'], $data['email'],
                $data['role'], password_hash($data['mot_de_passe'], PASSWORD_BCRYPT), $id
            ]);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE utilisateurs SET nom=?, prenom=?, email=?, role=? WHERE id=?"
            );
            $ok = $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $data['role'], $id]);
        }

        if (!empty($ok)) {
            $this->syncRoleSpecialization($id, (string) $data['role']);
        }

        return $ok;
    }

    public function updateIdentity(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE utilisateurs SET nom=?, prenom=?, email=? WHERE id=?"
        );
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE utilisateurs SET actif=0 WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function countByRole(string $role): int {
        if ($role === 'fournisseur') {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM utilisateurs u
                 JOIN fournisseurs f ON f.utilisateur_id = u.id
                 WHERE u.role = ? AND u.actif = 1"
            );
            $stmt->execute([$role]);
            return (int) $stmt->fetchColumn();
        }

        if (isset(self::ROLE_TABLE_MAP[$role])) {
            $table = self::ROLE_TABLE_MAP[$role];
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM utilisateurs u
                 JOIN {$table} t ON t.utilisateur_id = u.id
                 WHERE u.role = ? AND u.actif = 1"
            );
            $stmt->execute([$role]);
            return (int) $stmt->fetchColumn();
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE role=? AND actif=1");
        $stmt->execute([$role]);
        return (int) $stmt->fetchColumn();
    }

    private function syncRoleSpecialization(int $userId, string $role): void {
        foreach (self::ROLE_TABLE_MAP as $mappedRole => $table) {
            if ($mappedRole === $role) {
                $stmt = $this->db->prepare("INSERT IGNORE INTO {$table} (utilisateur_id) VALUES (?)");
                $stmt->execute([$userId]);
                continue;
            }

            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE utilisateur_id = ?");
            $stmt->execute([$userId]);
        }

        if ($role !== 'fournisseur') {
            $stmt = $this->db->prepare("UPDATE fournisseurs SET utilisateur_id = NULL WHERE utilisateur_id = ?");
            $stmt->execute([$userId]);
        }
    }
}
