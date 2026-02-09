<?php

class AuthController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Flight::get('pdo');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function isLoggedIn(): bool
    {
        $this->ensureSession();
        return !empty($_SESSION['user_id']);
    }

    private function json(array $data, int $code = 200): void
    {
        Flight::json($data, $code);
    }

    // ── Pages ───────────────────────────────────────────────

    /**
     * GET / — redirect to /index or /login
     */
    public function home(): void
    {
        Flight::redirect($this->isLoggedIn() ? '/index' : '/login');
    }

    /**
     * GET /login — show login form (forms.php)
     */
    public function showLogin(): void
    {
        if ($this->isLoggedIn()) {
            Flight::redirect('/index');
            return;
        }
        $loginError = '';
        include __DIR__ . '/../../public/forms.php';
    }

    /**
     * POST /login — handle login form submission, then show view on error
     */
    public function handleLogin(): void
    {
        $this->ensureSession();

        // Already logged in
        if ($this->isLoggedIn()) {
            Flight::redirect('/index');
            return;
        }

        $loginError = '';
        $formType   = trim($_POST['_form'] ?? 'contact');
        $email      = trim($_POST['email'] ?? '');

        // Parse name depending on form type
        if ($formType === 'register') {
            $username  = trim($_POST['username'] ?? '');
            $parts     = explode(' ', $username, 2);
            $firstName = $parts[0] ?? '';
            $lastName  = $parts[1] ?? $firstName;
        } else {
            $firstName = trim($_POST['firstName'] ?? '');
            $lastName  = trim($_POST['lastName'] ?? '');
        }

        // Validate
        if ($firstName === '' || $email === '') {
            $loginError = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $loginError = 'Adresse email invalide.';
        } else {
            try {
                $user = auto_login_or_create($this->pdo, $firstName, $lastName, $email);
                if ($user) {
                    Flight::redirect('/index');
                    return;
                }
                $loginError = 'Erreur lors de la connexion. Veuillez réessayer.';
            } catch (Exception $e) {
                error_log('Auto-login error: ' . $e->getMessage());
                $loginError = 'Erreur serveur. Veuillez réessayer plus tard.';
            }
        }

        // On error → re-render the form with $loginError
        include __DIR__ . '/../../public/forms.php';
    }

    /**
     * GET /logout
     */
    public function logout(): void
    {
        include __DIR__ . '/../../public/logout.php';
    }

    // ── API ─────────────────────────────────────────────────

    /**
     * POST /api/login-auto — auto-login by email (JSON API)
     */
    public function loginAuto(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = trim((string)($input['email'] ?? ''));

        if ($email === '') {
            $this->json(['error' => 'email required'], 400);
            return;
        }

        try {
            $stmt = $this->pdo->prepare(
                'SELECT id, firstName, lastName, email FROM users WHERE email = :email LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $this->json(['error' => 'internal error'], 500);
            return;
        }

        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
            return;
        }

        $this->ensureSession();
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = trim($user['firstName'] . ' ' . $user['lastName']);
        $_SESSION['user_email'] = $user['email'];

        $this->json(['success' => true, 'user' => $user]);
    }
}
