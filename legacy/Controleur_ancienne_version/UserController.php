<?php

require_once __DIR__ . '/../Modele/User.php';
require_once __DIR__ . '/../Modele/Database.php';

class UserController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM users ORDER BY nom');
        $rows = $stmt->fetchAll();
        return array_map([$this, 'hydrate'], $rows);
    }

    public function getById(int $id): ?User {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(User $user): bool {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, mot_de_passe, role)
             VALUES (:nom, :prenom, :email, :mdp, :role)'
        );
        return $stmt->execute([
            ':nom'    => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':email'  => $user->getEmail(),
            ':mdp'    => $user->getMotDePasse(),
            ':role'   => $user->getRole(),
        ]);
    }

    public function update(User $user): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET nom = :nom, prenom = :prenom, email = :email,
             mot_de_passe = :mdp, role = :role WHERE id = :id'
        );
        return $stmt->execute([
            ':nom'    => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':email'  => $user->getEmail(),
            ':mdp'    => $user->getMotDePasse(),
            ':role'   => $user->getRole(),
            ':id'     => $user->getId(),
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): User {
        return new User(
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['mot_de_passe'],
            $row['role'],
            (int) $row['id'],
            $row['created_at']
        );
    }
}
