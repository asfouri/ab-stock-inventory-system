<?php
// index.php - Point d'entrée principal (Front Controller)

require_once 'config/database.php';

const COOKIE_CONSENT_NAME = 'cookie_consent';
const COOKIE_CONSENT_ACCEPTED = 'accepted';
const COOKIE_CONSENT_LIFETIME = 31536000;

function hasCookieConsent(): bool {
    return ($_COOKIE[COOKIE_CONSENT_NAME] ?? '') === COOKIE_CONSENT_ACCEPTED;
}

function sanitizeRedirectTarget(?string $target): string {
    $target = trim((string) $target);
    if ($target === '' || str_contains($target, "\r") || str_contains($target, "\n")) {
        return 'index.php';
    }
    if (preg_match('#^https?://#i', $target)) {
        return 'index.php';
    }
    return $target;
}

function currentRequestTarget(): string {
    $requestUri = $_SERVER['REQUEST_URI'] ?? 'index.php';
    return sanitizeRedirectTarget($requestUri !== '' ? $requestUri : 'index.php');
}

function renderCookieConsentGate(bool $blocked, string $redirectTarget): void {
    $cookieBlocked = $blocked;
    $cookieRedirectTarget = $redirectTarget;
    $cookiePageTitle = $blocked ? 'Cookies requis' : 'Consentement aux cookies';
    require 'Vue/partials/cookie_consent.php';
    exit;
}

$page = $_GET['page'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cookie_action'])) {
    $cookieAction = (string) $_POST['cookie_action'];
    $redirectTarget = sanitizeRedirectTarget($_POST['redirect_to'] ?? 'index.php');

    if ($cookieAction === 'accept') {
        setcookie(
            COOKIE_CONSENT_NAME,
            COOKIE_CONSENT_ACCEPTED,
            [
                'expires' => time() + COOKIE_CONSENT_LIFETIME,
                'path' => '/',
                'samesite' => 'Lax',
            ]
        );
        header('Location: ' . $redirectTarget);
        exit;
    }

    if ($cookieAction === 'refuse') {
        header('Location: index.php?page=cookies_refused');
        exit;
    }
}

if (!hasCookieConsent()) {
    renderCookieConsentGate($page === 'cookies_refused', currentRequestTarget());
}

session_start();
require_once 'Modele/Database.php';
require_once 'Modele/BaseModel.php';
require_once 'Modele/Utilisateur.php';
require_once 'Modele/AppSettings.php';
require_once 'Modele/User.php';
require_once 'Modele/Product.php';
require_once 'Modele/SaleManager.php';
require_once 'Modele/CategorySupplier.php';
require_once 'Controleur/AuthController.php';
require_once 'Controleur/ProductController.php';
require_once 'Controleur/Controllers.php';

// Helper: vérification de rôle
$settingsModel = new AppSettings();
$GLOBALS['app_settings'] = $settingsModel->getAll();

function app_setting(string $key, mixed $default = null): mixed {
    return $GLOBALS['app_settings'][$key] ?? $default;
}

function app_name(): string {
    return (string) app_setting('app_name', APP_NAME);
}

function app_tva(): float {
    return (float) app_setting('tva_taux', TVA_TAUX);
}

function base_url(): string {
    return (string) app_setting('base_url', BASE_URL);
}

function ui_theme(): string {
    $theme = $_COOKIE['site_theme'] ?? 'light';
    return in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
}

function csrf_token(): string {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_input(): string {
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(): void {
    $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        $_SESSION['error'] = 'Session expirée ou requête invalide.';
        header('Location: index.php');
        exit;
    }
}

function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
}

function requireRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $roles, true)) {
        $_SESSION['error'] = 'Accès refusé : droits insuffisants.';
        header('Location: index.php');
        exit;
    }
}

$page = $_GET['page'] ?? '';
$action = $_GET['action'] ?? '';

if ($page === 'cookies_refused') {
    header('Location: index.php');
    exit;
}

// Pages publiques
if ($page === 'login') {
    $ctrl = new AuthController();
    $ctrl->login();
    exit;
}
if ($page === 'signup') {
    $ctrl = new AuthController();
    $ctrl->signup();
    exit;
}
if ($page === 'logout') {
    $ctrl = new AuthController();
    $ctrl->logout();
    exit;
}

// Toutes les autres pages nécessitent une connexion
requireLogin();

// Routing principal
switch ($page) {
    // ---- Dashboard (default) ----
    case '':
    case 'dashboard':
        if ($_SESSION['user_role'] === 'admin') {
            $ctrl = new AdminController();
            $ctrl->dashboard();
        } elseif ($_SESSION['user_role'] === 'fournisseur') {
            header('Location: index.php?page=produits');
        } else {
            header('Location: index.php?page=caisse');
        }
        break;

    // ---- Produits ----
    case 'produits':
        $ctrl = new ProductController();
        if ($action === 'store')  { $ctrl->store();  break; }
        if ($action === 'update') { $ctrl->update(); break; }
        if ($action === 'delete') { $ctrl->delete(); break; }
        if ($action === 'search') { $ctrl->apiSearch(); break; }
        $ctrl->index();
        break;

    // ---- Caisse / Vente ----
    case 'caisse':
        $ctrl = new SaleController();
        $ctrl->caisse();
        break;

    case 'valider_vente':
        $ctrl = new SaleController();
        $ctrl->valider();
        break;

    case 'historique':
        $ctrl = new SaleController();
        if ($action === 'clear') { $ctrl->clearHistory(); break; }
        if ($action === 'delete') { $ctrl->deleteHistoryItem(); break; }
        $ctrl->historique();
        break;

    case 'detail_vente':
        $ctrl = new SaleController();
        $ctrl->detail();
        break;

    // ---- Admin only ----
    case 'utilisateurs':
        $ctrl = new AdminController();
        $ctrl->utilisateurs();
        break;

    case 'categories':
        $ctrl = new AdminController();
        $ctrl->categories();
        break;

    case 'fournisseurs':
        $ctrl = new AdminController();
        $ctrl->fournisseurs();
        break;

    case 'parametres':
        $ctrl = new AdminController();
        $ctrl->parametres();
        break;

    default:
        http_response_code(404);
        echo "<h1>Page introuvable</h1><a href='index.php'>Retour</a>";
}
