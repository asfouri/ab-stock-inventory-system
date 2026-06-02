<?php

require_once __DIR__ . '/../Modele/Category.php';
require_once __DIR__ . '/../Modele/Database.php';

class CategoryController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY nom');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function getById(int $id): ?Category {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(Category $category): bool {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (nom, description) VALUES (:nom, :desc)'
        );
        return $stmt->execute([
            ':nom'  => $category->getNom(),
            ':desc' => $category->getDescription(),
        ]);
    }

    public function update(Category $category): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE categories SET nom = :nom, description = :desc WHERE id = :id'
        );
        return $stmt->execute([
            ':nom'  => $category->getNom(),
            ':desc' => $category->getDescription(),
            ':id'   => $category->getId(),
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): Category {
        return new Category(
            $row['nom'],
            $row['description'],
            (int) $row['id']
        );
    }
}
