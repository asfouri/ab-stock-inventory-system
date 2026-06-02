<?php
// Controleur/SaleController.php

class SaleController {
    private SaleManager $saleManager;

    public function __construct() {
        $this->saleManager = new SaleManager();
    }

    public function caisse(): void {
        requireRole(['admin', 'vendeur']);
        $productModel = new Product();
        $categoryModel = new Category();
        $produits = $productModel->getAll();
        $categories = $categoryModel->getAll();
        include 'Vue/vendeur/caisse.php';
    }

    public function valider(): void {
        requireRole(['admin', 'vendeur']);
        verify_csrf();
        header('Content-Type: application/json');

        $panier = json_decode(file_get_contents('php://input'), true);
        if (!is_array($panier)) {
            echo json_encode(['success' => false, 'message' => 'Donnees invalides.']);
            exit;
        }

        $result = $this->saleManager->createSale($_SESSION['user_id'], $panier);
        echo json_encode($result);
        exit;
    }

    public function historique(): void {
        requireRole(['admin', 'vendeur']);
        $vendeurId = ($_SESSION['user_role'] === 'vendeur') ? $_SESSION['user_id'] : 0;
        $ventes = $this->saleManager->getAll($vendeurId);
        include 'Vue/vendeur/historique.php';
    }

    public function clearHistory(): void {
        requireRole(['admin']);
        verify_csrf();

        try {
            $deletedCount = $this->saleManager->clearHistory();
            $_SESSION['success'] = $deletedCount > 0
                ? $deletedCount . ' vente(s) supprimee(s) de l historique.'
                : 'Aucune vente a supprimer.';
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Suppression de l historique impossible.';
        }

        header('Location: index.php?page=historique');
        exit;
    }

    public function deleteHistoryItem(): void {
        requireRole(['admin']);
        verify_csrf();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error'] = 'Vente introuvable.';
            header('Location: index.php?page=historique');
            exit;
        }

        try {
            $deleted = $this->saleManager->deleteById($id);
            $_SESSION[$deleted ? 'success' : 'error'] = $deleted
                ? 'La vente a ete supprimee de l historique.'
                : 'Vente introuvable.';
        } catch (Throwable $e) {
            $_SESSION['error'] = 'Suppression de la vente impossible.';
        }

        header('Location: index.php?page=historique');
        exit;
    }

    public function detail(): void {
        requireRole(['admin', 'vendeur']);
        $id = (int) ($_GET['id'] ?? 0);
        $vente = $this->saleManager->findById($id);
        if (!$vente) {
            $_SESSION['error'] = 'Vente introuvable.';
            header('Location: index.php?page=historique');
            exit;
        }
        if ($_SESSION['user_role'] === 'vendeur' && $vente['vendeur_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Acces refuse.';
            header('Location: index.php?page=historique');
            exit;
        }
        include 'Vue/vendeur/detail_vente.php';
    }
}

// Controleur/AdminController.php

class AdminController {
    private User $userModel;
    private SaleManager $saleManager;
    private Product $productModel;
    private Category $categoryModel;
    private Supplier $supplierModel;
    private AppSettings $settingsModel;

    public function __construct() {
        $this->userModel = new User();
        $this->saleManager = new SaleManager();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->supplierModel = new Supplier();
        $this->settingsModel = new AppSettings();
    }

    public function dashboard(): void {
        requireRole(['admin']);
        $ca = $this->saleManager->getChiffreAffaires();
        $caMois = $this->saleManager->getChiffreAffairesMois();
        $topProduits = $this->saleManager->getTopProduits(5);
        $lowStock = $this->productModel->getLowStock();
        $nbProduits = $this->productModel->count();
        $nbVendeurs = $this->userModel->countByRole('vendeur');
        $stockValue = $this->productModel->getTotalStockValue();
        include 'Vue/admin/dashboard.php';
    }

    public function utilisateurs(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $action = $_POST['action'] ?? '';
            $db = Database::getInstance();

            try {
                if ($action === 'create') {
                    $db->beginTransaction();
                    $userId = $this->userModel->createAndGetId($_POST);
                    if (($_POST['role'] ?? '') === 'fournisseur') {
                        $this->ensureSupplierProfileForUser($_POST, $userId);
                    }
                    $db->commit();
                    $_SESSION['success'] = 'Utilisateur cree.';
                } elseif ($action === 'update') {
                    $db->beginTransaction();
                    $ok = $this->userModel->update((int) $_POST['id'], $_POST);
                    if (!$ok) {
                        throw new RuntimeException('Erreur mise a jour.');
                    }
                    if (($_POST['role'] ?? '') === 'fournisseur') {
                        $this->ensureSupplierProfileForUser($_POST, (int) $_POST['id']);
                    }
                    $db->commit();
                    $_SESSION['success'] = 'Utilisateur mis a jour.';
                } elseif ($action === 'delete') {
                    $ok = $this->userModel->delete((int) $_POST['id']);
                    $_SESSION[$ok ? 'success' : 'error'] = $ok ? 'Utilisateur desactive.' : 'Erreur suppression.';
                }
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $_SESSION['error'] = 'Operation impossible sur l utilisateur.';
            }

            header('Location: index.php?page=utilisateurs');
            exit;
        }
        $utilisateurs = $this->userModel->getAll();
        include 'Vue/admin/utilisateurs.php';
    }

    public function categories(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $action = $_POST['action'] ?? '';
            if ($action === 'create') {
                $this->categoryModel->create($_POST);
                $_SESSION['success'] = 'Categorie ajoutee.';
            } elseif ($action === 'update') {
                $this->categoryModel->update((int) $_POST['id'], $_POST);
                $_SESSION['success'] = 'Categorie mise a jour.';
            } elseif ($action === 'delete') {
                $this->categoryModel->delete((int) $_POST['id']);
                $_SESSION['success'] = 'Categorie supprimee.';
            }
            header('Location: index.php?page=categories');
            exit;
        }
        $categories = $this->categoryModel->getAll();
        include 'Vue/admin/categories.php';
    }

    public function fournisseurs(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $action = $_POST['action'] ?? '';
            $db = Database::getInstance();

            try {
                if ($action === 'create') {
                    $db->beginTransaction();

                    $accountMode = $_POST['account_mode'] ?? 'create';
                    if ($accountMode === 'create') {
                        $email = trim($_POST['email'] ?? '');
                        $password = $_POST['user_password'] ?? '';

                        if ($email === '' || $password === '') {
                            throw new RuntimeException('Email et mot de passe requis pour creer un compte fournisseur.');
                        }

                        if ($this->userModel->findAnyByEmail($email)) {
                            throw new RuntimeException('Cet email existe deja dans utilisateurs.');
                        }

                        $_POST['utilisateur_id'] = $this->userModel->createAndGetId(
                            $this->buildSupplierUserPayload($_POST, $password)
                        );
                    } elseif ($accountMode === 'link_existing' && !empty($_POST['utilisateur_id'])) {
                        $linkedUser = $this->userModel->findById((int) $_POST['utilisateur_id']);
                        if (!$linkedUser) {
                            throw new RuntimeException('Utilisateur lie introuvable.');
                        }
                    } else {
                        $_POST['utilisateur_id'] = $_POST['utilisateur_id'] ?? null;
                    }

                    $this->supplierModel->create($_POST);
                    $db->commit();
                    $_SESSION['success'] = 'Fournisseur ajoute.';
                } elseif ($action === 'update') {
                    $db->beginTransaction();
                    $supplierId = (int) $_POST['id'];
                    $existingSupplier = $this->supplierModel->findById($supplierId);

                    if (!$existingSupplier) {
                        throw new RuntimeException('Fournisseur introuvable.');
                    }

                    $this->supplierModel->update($supplierId, $_POST);

                    if (!empty($existingSupplier['utilisateur_id'])) {
                        $this->syncSupplierUserAccount((int) $existingSupplier['utilisateur_id'], $_POST);
                    }

                    $db->commit();
                    $_SESSION['success'] = 'Fournisseur mis a jour.';
                } elseif ($action === 'delete') {
                    $this->supplierModel->delete((int) $_POST['id']);
                    $_SESSION['success'] = 'Fournisseur supprime.';
                } elseif ($action === 'sync_accounts') {
                    $created = $this->syncMissingSupplierAccounts();
                    $_SESSION['success'] = $created > 0
                        ? $created . ' compte(s) fournisseur crees dans utilisateurs. Mot de passe temporaire : password'
                        : 'Aucun fournisseur supplementaire a synchroniser.';
                }
            } catch (Throwable $e) {
                $db = Database::getInstance();
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $_SESSION['error'] = $e instanceof RuntimeException ? $e->getMessage() : 'Erreur lors du traitement du fournisseur.';
            }

            header('Location: index.php?page=fournisseurs');
            exit;
        }
        $fournisseurs = $this->supplierModel->getAll();
        include 'Vue/admin/fournisseurs.php';
    }

    private function buildSupplierUserPayload(array $supplierData, string $password): array {
        $identity = $this->buildSupplierUserIdentity($supplierData);

        return [
            'nom' => $identity['nom'],
            'prenom' => $identity['prenom'],
            'email' => $identity['email'],
            'mot_de_passe' => $password,
            'role' => 'fournisseur',
        ];
    }

    private function buildSupplierUserIdentity(array $supplierData): array {
        $contact = trim((string) ($supplierData['contact'] ?? ''));
        $supplierName = trim((string) ($supplierData['nom'] ?? ''));

        return [
            'nom' => $supplierName !== '' ? $supplierName : 'Fournisseur',
            'prenom' => $contact !== '' ? $contact : 'Compte',
            'email' => trim((string) ($supplierData['email'] ?? '')),
        ];
    }

    private function syncSupplierUserAccount(int $userId, array $supplierData): void {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            throw new RuntimeException('Compte fournisseur introuvable.');
        }

        $identity = $this->buildSupplierUserIdentity($supplierData);
        if ($identity['email'] === '') {
            $identity['email'] = (string) $user['email'];
        }

        $existingUser = $this->userModel->findAnyByEmail($identity['email']);
        if ($existingUser && (int) $existingUser['id'] !== $userId) {
            throw new RuntimeException('Cet email existe deja dans utilisateurs.');
        }

        $this->userModel->updateIdentity($userId, $identity);
    }

    private function ensureSupplierProfileForUser(array $userData, int $userId): void {
        $existingSupplier = $this->supplierModel->findByUserId($userId);
        if ($existingSupplier) {
            return;
        }

        $fullName = trim(($userData['prenom'] ?? '') . ' ' . ($userData['nom'] ?? ''));
        $this->supplierModel->create([
            'nom' => $fullName !== '' ? $fullName : ($userData['nom'] ?? 'Fournisseur'),
            'contact' => $fullName,
            'email' => $userData['email'] ?? '',
            'telephone' => '',
            'adresse' => '',
            'utilisateur_id' => $userId,
        ]);
    }

    private function syncMissingSupplierAccounts(): int {
        $missingSuppliers = $this->supplierModel->getWithoutUser();
        if (empty($missingSuppliers)) {
            return 0;
        }

        $db = Database::getInstance();
        $created = 0;
        $db->beginTransaction();

        try {
            foreach ($missingSuppliers as $supplier) {
                $existingUser = $this->userModel->findAnyByEmail((string) $supplier['email']);
                if ($existingUser) {
                    $this->supplierModel->linkUser((int) $supplier['id'], (int) $existingUser['id']);
                    continue;
                }

                $userId = $this->userModel->createAndGetId($this->buildSupplierUserPayload($supplier, 'password'));
                $this->supplierModel->linkUser((int) $supplier['id'], $userId);
                $created++;
            }

            $db->commit();
            return $created;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    public function parametres(): void {
        requireRole(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $appName = trim($_POST['app_name'] ?? '');
            $baseUrl = trim($_POST['base_url'] ?? '');
            $tvaTaux = trim($_POST['tva_taux'] ?? '');

            if ($appName === '' || $baseUrl === '' || !is_numeric($tvaTaux)) {
                $_SESSION['error'] = 'Veuillez renseigner des parametres valides.';
                header('Location: index.php?page=parametres');
                exit;
            }

            $tvaValue = round((float) $tvaTaux, 2);
            if ($tvaValue < 0 || $tvaValue > 100) {
                $_SESSION['error'] = 'Le taux de TVA doit etre compris entre 0 et 100.';
                header('Location: index.php?page=parametres');
                exit;
            }

            $ok = $this->settingsModel->updateMany([
                'app_name' => $appName,
                'base_url' => rtrim($baseUrl, '/'),
                'tva_taux' => number_format($tvaValue, 2, '.', ''),
            ]);

            $_SESSION[$ok ? 'success' : 'error'] = $ok
                ? 'Configuration globale mise a jour.'
                : 'Erreur lors de la mise a jour de la configuration.';

            header('Location: index.php?page=parametres');
            exit;
        }

        $settings = $this->settingsModel->getAll();
        include 'Vue/admin/settings.php';
    }
}
