<?php
// Controleur/ProductController.php

class ProductController {
    private Product $productModel;
    private Category $categoryModel;
    private Supplier $supplierModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
    }

    public function index(): void {
        $filters = [
            'categorie_id' => $_GET['categorie_id'] ?? '',
            'fournisseur_id' => $_GET['fournisseur_id'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];
        $limitOptions = ['5', '10', '20', '50', '100', 'all'];
        $selectedLimit = (string) ($_GET['limit'] ?? '10');
        if (!in_array($selectedLimit, $limitOptions, true)) {
            $selectedLimit = '10';
        }
        if ($_SESSION['user_role'] === 'fournisseur') {
            $filters['fournisseur_user_id'] = $_SESSION['user_id'];
        }
        $produits = $this->productModel->getAll($filters);
        $totalProduits = count($produits);
        if ($selectedLimit !== 'all') {
            $produits = array_slice($produits, 0, (int) $selectedLimit);
        }
        $categories = $this->categoryModel->getAll();
        $fournisseurs = $this->supplierModel->getAll();
        $lowStock = $this->productModel->getLowStock();
        include 'Vue/admin/produits.php';
    }

    public function store(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $result = $this->productModel->create($_POST);
            $_SESSION[$result ? 'success' : 'error'] = $result ? 'Produit ajoute avec succes.' : 'Erreur lors de l ajout.';
        }
        header('Location: index.php?page=produits');
        exit;
    }

    public function update(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $id = (int) ($_POST['id'] ?? 0);
            $result = $this->productModel->update($id, $_POST);
            $_SESSION[$result ? 'success' : 'error'] = $result ? 'Produit mis a jour.' : 'Erreur de mise a jour.';
        }
        header('Location: index.php?page=produits');
        exit;
    }

    public function delete(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=produits');
            exit;
        }
        verify_csrf();
        $id = (int) ($_POST['id'] ?? 0);
        $result = $this->productModel->delete($id);

        if (($result['success'] ?? false) === true) {
            $_SESSION['success'] = 'Produit supprime definitivement.';
        } elseif (($result['reason'] ?? '') === 'referenced') {
            $_SESSION['error'] = 'Suppression impossible : ce produit est deja utilise dans l historique des ventes.';
        } elseif (($result['reason'] ?? '') === 'not_found') {
            $_SESSION['error'] = 'Produit introuvable.';
        } else {
            $_SESSION['error'] = 'Erreur de suppression.';
        }

        header('Location: index.php?page=produits');
        exit;
    }

    public function apiSearch(): void {
        requireRole(['admin', 'vendeur', 'fournisseur']);
        header('Content-Type: application/json');
        $filters = ['search' => $_GET['q'] ?? ''];
        if ($_SESSION['user_role'] === 'fournisseur') {
            $filters['fournisseur_user_id'] = $_SESSION['user_id'];
        }
        $prods = $this->productModel->getAll($filters);
        echo json_encode($prods);
        exit;
    }
}
