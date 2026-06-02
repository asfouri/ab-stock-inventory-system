<?php

require_once __DIR__ . '/../Modele/Sale.php';
require_once __DIR__ . '/../Modele/SaleItem.php';
require_once __DIR__ . '/../Modele/Cart.php';
require_once __DIR__ . '/../Modele/Database.php';
require_once __DIR__ . '/ProductController.php';

class SaleController {
    private PDO               $pdo;
    private ProductController $productController;

    public function __construct() {
        $this->pdo               = Database::getInstance()->getPdo();
        $this->productController = new ProductController();
    }

    /**
     * Validates the cart, persists the sale + items, decrements stock.
     * Returns the new Sale on success, null on failure (e.g. insufficient stock).
     */
    public function validateCart(Cart $cart, int $vendeurId): ?Sale {
        if ($cart->isEmpty()) {
            return null;
        }

        $this->pdo->beginTransaction();
        try {
            // Decrement stock for each item first
            foreach ($cart->getItems() as $item) {
                $ok = $this->productController->decrementStock(
                    $item->getProductId(),
                    $item->getQuantite()
                );
                if (!$ok) {
                    $this->pdo->rollBack();
                    return null; // insufficient stock
                }
            }

            // Insert sale record
            $dateVente = date('Y-m-d H:i:s');
            $stmt = $this->pdo->prepare(
                'INSERT INTO sales (vendeur_id, date_vente, total_ht, total_ttc)
                 VALUES (:vid, :date, :ht, :ttc)'
            );
            $stmt->execute([
                ':vid'  => $vendeurId,
                ':date' => $dateVente,
                ':ht'   => $cart->getTotalHT(),
                ':ttc'  => $cart->getTotalTTC(),
            ]);
            $saleId = (int) $this->pdo->lastInsertId();

            // Insert sale items
            $stmtItem = $this->pdo->prepare(
                'INSERT INTO sale_items (sale_id, product_id, product_nom, quantite, prix_unitaire)
                 VALUES (:sid, :pid, :pnom, :qty, :pu)'
            );
            $saleItems = [];
            foreach ($cart->getItems() as $item) {
                $stmtItem->execute([
                    ':sid'  => $saleId,
                    ':pid'  => $item->getProductId(),
                    ':pnom' => $item->getProductNom(),
                    ':qty'  => $item->getQuantite(),
                    ':pu'   => $item->getPrixUnitaire(),
                ]);
                $saleItems[] = new SaleItem(
                    $saleId,
                    $item->getProductId(),
                    $item->getProductNom(),
                    $item->getQuantite(),
                    $item->getPrixUnitaire(),
                    (int) $this->pdo->lastInsertId()
                );
            }

            $this->pdo->commit();

            $sale = new Sale(
                $vendeurId,
                $dateVente,
                $cart->getTotalHT(),
                $cart->getTotalTTC(),
                $saleItems,
                $saleId
            );

            $cart->clear();
            return $sale;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            'SELECT * FROM sales ORDER BY date_vente DESC'
        );
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function getByVendeur(int $vendeurId): array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM sales WHERE vendeur_id = :vid ORDER BY date_vente DESC'
        );
        $stmt->execute([':vid' => $vendeurId]);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function getById(int $id): ?Sale {
        $stmt = $this->pdo->prepare('SELECT * FROM sales WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $sale  = $this->hydrate($row);
        $items = $this->getItemsBySaleId($id);
        $sale->setItems($items);
        return $sale;
    }

    private function getItemsBySaleId(int $saleId): array {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM sale_items WHERE sale_id = :sid'
        );
        $stmt->execute([':sid' => $saleId]);
        return array_map(function (array $row): SaleItem {
            return new SaleItem(
                (int)   $row['sale_id'],
                (int)   $row['product_id'],
                        $row['product_nom'],
                (int)   $row['quantite'],
                (float) $row['prix_unitaire'],
                (int)   $row['id']
            );
        }, $stmt->fetchAll());
    }

    private function hydrate(array $row): Sale {
        return new Sale(
            (int)   $row['vendeur_id'],
                    $row['date_vente'],
            (float) $row['total_ht'],
            (float) $row['total_ttc'],
            [],
            (int)   $row['id']
        );
    }
}
