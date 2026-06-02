<?php

require_once __DIR__ . '/../Modele/User.php';
require_once __DIR__ . '/../Modele/Database.php';

class AuthController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function login(string $email, string $plainPassword): ?User {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($plainPassword, $row['mot_de_passe'])) {
            return null;
        }

        $user = new User(
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['mot_de_passe'],
            $row['role'],
            (int) $row['id'],
            $row['created_at']
        );

        $_SESSION['user_id']   = $user->getId();
        $_SESSION['user_role'] = $user->getRole();

        return $user;
    }

    public function logout(): void {
        session_destroy();
        header('Location: ../Vue/login.php');
        exit;
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public function requireRole(string ...$roles): void {
        if (!$this->isLoggedIn() || !in_array($_SESSION['user_role'], $roles, true)) {
            header('HTTP/1.1 403 Forbidden');
            header('Location: ../Vue/login.php');
            exit;
        }
    }
}
