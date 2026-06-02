<?php
// Controleur/AuthController.php

class AuthController {
    private User $userModel;
    private Supplier $supplierModel;

    public function __construct() {
        $this->userModel = new User();
        $this->supplierModel = new Supplier();
    }

    public function login(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs.';
                header('Location: index.php?page=login');
                exit;
            }

            $supplier = null;
            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                $supplier = $this->supplierModel->findByEmail($email);
                if ($supplier && !empty($supplier['utilisateur_id'])) {
                    $user = $this->userModel->findActiveById((int) $supplier['utilisateur_id']);
                }
            }

            if ($user && $this->userModel->verifyPassword($password, $user['mot_de_passe'])) {
                $utilisateur = $this->userModel->hydrateUtilisateur($user);
                session_regenerate_id(true);
                foreach ($utilisateur->toSessionData() as $key => $value) {
                    $_SESSION[$key] = $value;
                }
                $_SESSION['success'] = 'Bienvenue, ' . $user['prenom'] . ' !';
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = ($supplier && empty($supplier['utilisateur_id']))
                    ? 'Ce fournisseur n a pas encore de compte de connexion.'
                    : 'Email ou mot de passe incorrect.';
                header('Location: index.php?page=login');
                exit;
            }
        }
        include 'Vue/auth/login.php';
    }

    public function signup(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $data = [
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => trim($_POST['role'] ?? 'vendeur'),
                'mot_de_passe' => $_POST['mot_de_passe'] ?? '',
                'mot_de_passe_confirmation' => $_POST['mot_de_passe_confirmation'] ?? '',
            ];

            $_SESSION['old_signup'] = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'role' => $data['role'],
            ];

            if (in_array('', [$data['nom'], $data['prenom'], $data['email'], $data['mot_de_passe'], $data['mot_de_passe_confirmation']], true)) {
                $_SESSION['error'] = 'Veuillez remplir tous les champs.';
                header('Location: index.php?page=signup');
                exit;
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Adresse email invalide.';
                header('Location: index.php?page=signup');
                exit;
            }

            if (!in_array($data['role'], ['admin', 'vendeur', 'fournisseur'], true)) {
                $_SESSION['error'] = 'Role invalide.';
                header('Location: index.php?page=signup');
                exit;
            }

            if (strlen($data['mot_de_passe']) < 8) {
                $_SESSION['error'] = 'Le mot de passe doit contenir au moins 8 caracteres.';
                header('Location: index.php?page=signup');
                exit;
            }

            if ($data['mot_de_passe'] !== $data['mot_de_passe_confirmation']) {
                $_SESSION['error'] = 'La confirmation du mot de passe ne correspond pas.';
                header('Location: index.php?page=signup');
                exit;
            }

            if ($this->userModel->emailExists($data['email'])) {
                $_SESSION['error'] = 'Cet email est deja utilise.';
                header('Location: index.php?page=signup');
                exit;
            }

            $db = Database::getInstance();

            try {
                $db->beginTransaction();

                $userId = $this->userModel->createAndGetId([
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'email' => $data['email'],
                    'mot_de_passe' => $data['mot_de_passe'],
                    'role' => $data['role'],
                ]);

                if ($data['role'] === 'fournisseur') {
                    $fullName = trim($data['prenom'] . ' ' . $data['nom']);
                    $this->supplierModel->create([
                        'nom' => $fullName !== '' ? $fullName : $data['nom'],
                        'contact' => $fullName,
                        'email' => $data['email'],
                        'telephone' => '',
                        'adresse' => '',
                        'utilisateur_id' => $userId,
                    ]);
                }

                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $_SESSION['error'] = 'Impossible de creer le compte pour le moment.';
                header('Location: index.php?page=signup');
                exit;
            }

            unset($_SESSION['old_signup']);
            $_SESSION['success'] = 'Compte cree avec succes. Vous pouvez maintenant vous connecter.';
            header('Location: index.php?page=login');
            exit;
        }

        include 'Vue/auth/signup.php';
    }

    public function logout(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        verify_csrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
