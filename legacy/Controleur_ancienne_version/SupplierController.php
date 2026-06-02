<?php

require_once __DIR__ . '/../Modele/Supplier.php';
require_once __DIR__ . '/../Modele/Database.php';

class SupplierController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM suppliers ORDER BY nom');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function getById(int $id): ?Supplier {
        $stmt = $this->pdo->prepare('SELECT * FROM suppliers WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(Supplier $supplier): bool {
        $stmt = $this->pdo->prepare(
            'INSERT INTO suppliers (nom, email, telephone, adresse)
             VALUES (:nom, :email, :tel, :adr)'
        );
        return $stmt->execute([
            ':nom'   => $supplier->getNom(),
            ':email' => $supplier->getEmail(),
            ':tel'   => $supplier->getTelephone(),
            ':adr'   => $supplier->getAdresse(),
        ]);
    }

    public function update(Supplier $supplier): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE suppliers SET nom = :nom, email = :email,
             telephone = :tel, adresse = :adr WHERE id = :id'
        );
        return $stmt->execute([
            ':nom'   => $supplier->getNom(),
            ':email' => $supplier->getEmail(),
            ':tel'   => $supplier->getTelephone(),
            ':adr'   => $supplier->getAdresse(),
            ':id'    => $supplier->getId(),
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM suppliers WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function hydrate(array $row): Supplier {
        return new Supplier(
            $row['nom'],
            $row['email'],
            $row['telephone'],
            $row['adresse'],
            (int) $row['id']
        );
    }
}
