<?php

require_once __DIR__ . '/../Modele/Product.php';
require_once __DIR__ . '/../Modele/Database.php';

class ProductController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM products ORDER BY nom');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function getById(int $id): ?Product {
        $stmt = $this->pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function search(string $nom = '', int $categoryId = 0, int $supplierId = 0): array {
        $sql    = 'SELECT * FROM products WHERE 1=1';
        $params = [];

        if ($nom !== '') {
            $sql .= ' AND nom LIKE :nom';
            $params[':nom'] = '%' . $nom . '%';
        }
        if ($categoryId > 0) {
            $sql .= ' AND category_id = :cat';
            $params[':cat'] = $categoryId;
        }
        if ($supplierId > 0) {
            $sql .= ' AND supplier_id = :sup';
            $params[':sup'] = $supplierId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function getLowStock(): array {
        $stmt = $this->pdo->query(
            'SELECT * FROM products WHERE quantite_stock <= seuil_alerte'
        );
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function getBySupplierId(int $supplierId): array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM products WHERE supplier_id = :sid ORDER BY nom'
        );
        $stmt->execute([':sid' => $supplierId]);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function create(Product $product): bool {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (nom, description, prix_achat, prix_vente,
             quantite_stock, seuil_alerte, category_id, supplier_id)
             VALUES (:nom, :desc, :pa, :pv, :qty, :seuil, :cat, :sup)'
        );
        return $stmt->execute($this->bind($product));
    }

    public function update(Product $product): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE products SET nom = :nom, description = :desc,
             prix_achat = :pa, prix_vente = :pv, quantite_stock = :qty,
             seuil_alerte = :seuil, category_id = :cat, supplier_id = :sup
             WHERE id = :id'
        );
        $params       = $this->bind($product);
        $params[':id'] = $product->getId();
        return $stmt->execute($params);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function decrementStock(int $productId, int $qty): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE products SET quantite_stock = quantite_stock - :qty
             WHERE id = :id AND quantite_stock >= :qty'
        );
        $stmt->execute([':qty' => $qty, ':id' => $productId]);
        return $stmt->rowCount() > 0;
    }

    private function bind(Product $p): array {
        return [
            ':nom'   => $p->getNom(),
            ':desc'  => $p->getDescription(),
            ':pa'    => $p->getPrixAchat(),
            ':pv'    => $p->getPrixVente(),
            ':qty'   => $p->getQuantiteStock(),
            ':seuil' => $p->getSeuilAlerte(),
            ':cat'   => $p->getCategoryId(),
            ':sup'   => $p->getSupplierId(),
        ];
    }

    private function hydrate(array $row): Product {
        return new Product(
            $row['nom'],
            $row['description'],
            (float) $row['prix_achat'],
            (float) $row['prix_vente'],
            (int)   $row['quantite_stock'],
            (int)   $row['seuil_alerte'],
            (int)   $row['category_id'],
            (int)   $row['supplier_id'],
            (int)   $row['id']
        );
    }
}
