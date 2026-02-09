<?php

class PageController
{
    // ── Helpers ──────────────────────────────────────────────

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function requireAuth(): bool
    {
        $this->ensureSession();
        if (empty($_SESSION['user_id'])) {
            Flight::redirect('/login');
            return false;
        }
        return true;
    }

    /**
     * Load user info + unread count from DB, store in Flight view vars.
     * Views can then simply use $total_unread, $_SESSION['user_name'], etc.
     */
    private function loadUserData(): void
    {
        $total_unread = 0;
        try {
            $pdo = Flight::get('pdo');
            if ($pdo) {
                // Refresh user info
                $stmt = $pdo->prepare("SELECT id, CONCAT(firstName, ' ', lastName) AS name, email FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$_SESSION['user_id']]);
                $u = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($u) {
                    $_SESSION['user_name']  = $u['name'];
                    $_SESSION['user_email'] = $u['email'];
                }

                // Count unread messages
                $stmt2 = $pdo->prepare('SELECT COUNT(*) as c FROM messages WHERE recipient_id = ? AND is_read = 0');
                $stmt2->execute([$_SESSION['user_id']]);
                $total_unread = (int)($stmt2->fetch(PDO::FETCH_ASSOC)['c'] ?? 0);
            }
        } catch (Exception $e) {
            // graceful fallback
        }
        // Make $total_unread available in all views
        Flight::view()->set('total_unread', $total_unread);
    }

    /**
     * Render a page from public/ with auth + user data loaded.
     */
    private function renderPage(string $file): void
    {
        if (!$this->requireAuth()) return;
        $this->loadUserData();
        $total_unread = Flight::view()->get('total_unread');
        include __DIR__ . '/../../public/' . $file;
    }

    /**
     * Render a page from app/views/ with auth + user data loaded.
     */
    private function renderView(string $file): void
    {
        if (!$this->requireAuth()) return;
        $this->loadUserData();
        $total_unread = Flight::view()->get('total_unread');
        include __DIR__ . '/../views/' . $file;
    }

    // ── Pages ───────────────────────────────────────────────

    public function index(): void      { $this->renderPage('index-page.php'); }
    public function dashboard(): void  { $this->renderView('dashboard.php'); }
    public function analytics(): void  { $this->renderPage('analytics.php'); }
    public function calendar(): void   { $this->renderPage('calendar.php'); }
    public function elements(): void   { $this->renderPage('elements.php'); }
    public function elementsAlerts(): void  { $this->renderPage('elements-alerts.php'); }
    public function elementsBadges(): void { $this->renderPage('elements-badges.php'); }
    public function elementsButtons(): void { $this->renderPage('elements-buttons.php'); }
    public function elementsCards(): void   { $this->renderPage('elements-cards.php'); }
    public function elementsForms(): void   { $this->renderPage('elements-forms.php'); }
    public function elementsModals(): void  { $this->renderPage('elements-modals.php'); }
    public function elementsTables(): void  { $this->renderPage('elements-tables.php'); }
    public function files(): void      { $this->renderPage('files.php'); }
    public function help(): void       { $this->renderPage('help.php'); }
    public function orders(): void     { $this->renderPage('orders.php'); }
    public function products(): void   { $this->renderPage('products.php'); }
    public function profile(): void    { $this->renderPage('profile.php'); }
    public function reports(): void    { $this->renderPage('reports.php'); }
    public function security(): void   { $this->renderPage('security.php'); }
    public function settings(): void   { $this->renderPage('settings.php'); }
    public function users(): void      { $this->renderPage('users.php'); }

    // ── Messages ────────────────────────────────────────────

    /**
     * GET /messages — show messages page with data loaded from controller
     */
    public function messagesShow(): void
    {
        if (!$this->requireAuth()) return;
        $this->loadUserData();
        $total_unread = Flight::view()->get('total_unread');

        $controller = new MessagesController();
        $data = $controller->prepareMessagesPageData();
        extract($data);

        // Simple helpers for the view
        if (!function_exists('getInitial')) {
            function getInitial($name) { return $name ? strtoupper($name[0]) : '?'; }
        }
        if (!function_exists('formatTime')) {
            function formatTime($d) {
                if (!$d) return '';
                $dt = new DateTime($d); $now = new DateTime(); $diff = $now->diff($dt);
                if ($diff->days == 0) return $dt->format('H:i');
                if ($diff->days == 1) return 'Yesterday ' . $dt->format('H:i');
                if ($diff->days < 7) return $dt->format('D H:i');
                return $dt->format('d/m/Y H:i');
            }
        }

        include __DIR__ . '/../../public/messages.php';
    }

    /**
     * POST /messages — handle send_message / mark_conversation_read via form
     */
    public function messagesPost(): void
    {
        $this->ensureSession();
        if (empty($_SESSION['user_id'])) {
            Flight::redirect('/login');
            return;
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
               && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Merge JSON body if applicable
        $json = json_decode(file_get_contents('php://input'), true);
        if ($json) $_POST = array_merge($_POST, $json);

        $action = $_POST['action'] ?? '';
        $controller = new MessagesController();

        if ($action === 'send_message') {
            $recipientId = (int)($_POST['recipient_id'] ?? 0);
            $content     = trim($_POST['content'] ?? '');

            if ($recipientId && $content !== '') {
                try {
                    $controller->sendMessageDirect($_SESSION['user_id'], $recipientId, $content);
                    if ($isAjax) { Flight::json(['success' => true]); return; }
                    Flight::redirect('/messages?conversation=' . $recipientId);
                } catch (Exception $e) {
                    if ($isAjax) { Flight::json(['success' => false, 'error' => $e->getMessage()], 500); return; }
                    Flight::redirect('/messages');
                }
            } else {
                if ($isAjax) { Flight::json(['success' => false, 'error' => 'Destinataire ou message manquant'], 400); return; }
                Flight::redirect('/messages');
            }
            return;
        }

        if ($action === 'mark_conversation_read') {
            $otherUserId = (int)($_POST['other_user_id'] ?? 0);
            if ($otherUserId) {
                try {
                    $controller->markConversationReadDirect($_SESSION['user_id'], $otherUserId);
                    if ($isAjax) { Flight::json(['success' => true]); return; }
                } catch (Exception $e) {
                    if ($isAjax) { Flight::json(['success' => false, 'error' => $e->getMessage()], 500); return; }
                }
            } else {
                if ($isAjax) { Flight::json(['success' => false, 'error' => 'other_user_id manquant'], 400); return; }
            }
            Flight::redirect('/messages' . ($otherUserId ? '?conversation=' . $otherUserId : ''));
            return;
        }

        // Unknown action — just show the page
        $this->messagesShow();
    }
}
