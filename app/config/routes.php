<?php

use app\middlewares\SecurityHeadersMiddleware;

/**
 * ──────────────────────────────────────────────────────────────
 *  FlightPHP Routes — All GET & POST routes in one clean file
 * ──────────────────────────────────────────────────────────────
 */

// ── Global middleware ────────────────────────────────────────
Flight::before('start', function () {
    (new SecurityHeadersMiddleware())();
});

// ══════════════════════════════════════════════════════════════
//  AUTH ROUTES
// ══════════════════════════════════════════════════════════════
Flight::route('GET  /',       [new AuthController(), 'home']);
Flight::route('GET  /login',  [new AuthController(), 'showLogin']);
Flight::route('POST /login',  [new AuthController(), 'handleLogin']);
Flight::route('GET  /logout', [new AuthController(), 'logout']);

// ══════════════════════════════════════════════════════════════
//  PAGE ROUTES  (all auth-protected via PageController)
// ══════════════════════════════════════════════════════════════
$pages = [
    'index', 'dashboard', 'analytics', 'calendar',
    'elements', 'elements-alerts', 'elements-badges', 'elements-buttons',
    'elements-cards', 'elements-forms', 'elements-modals', 'elements-tables',
    'files', 'help', 'orders', 'products',
    'profile', 'reports', 'security', 'settings', 'users',
];

foreach ($pages as $slug) {
    // "elements-alerts" → "elementsAlerts"
    $method = lcfirst(str_replace('-', '', ucwords($slug, '-')));
    Flight::route("GET /$slug", [new PageController(), $method]);
}

// Messages (GET + POST handled separately)
Flight::route('GET  /messages', [new PageController(), 'messagesShow']);
Flight::route('POST /messages', [new PageController(), 'messagesPost']);

// ══════════════════════════════════════════════════════════════
//  API ROUTES
// ══════════════════════════════════════════════════════════════

// — Auth API —
Flight::route('POST /api/login-auto', [new AuthController(), 'loginAuto']);

// — Users API —
Flight::route('GET /api/users/list', [new MessagesController(), 'listUsers']);

// — Messages API —
Flight::route('POST /api/messages/send',                   [new MessagesController(), 'sendMessage']);
Flight::route('GET  /api/messages/conversation',           [new MessagesController(), 'fetchConversation']);
Flight::route('GET  /api/messages/for-me',                 [new MessagesController(), 'fetchForUser']);
Flight::route('GET  /api/messages/since',                  [new MessagesController(), 'getMessagesSince']);
Flight::route('POST /api/messages/mark-read',              [new MessagesController(), 'markRead']);
Flight::route('POST /api/messages/mark-conversation-read', [new MessagesController(), 'markConversationRead']);
Flight::route('GET  /api/messages/conversations',          [new MessagesController(), 'getConversations']);
Flight::route('GET  /api/messages/stats',                  [new MessagesController(), 'getMessageStats']);
Flight::route('GET  /api/messages/unread-counts',          [new MessagesController(), 'getUnreadCounts']);
Flight::route('GET  /api/messages/search',                 [new MessagesController(), 'searchMessages']);
Flight::route('GET  /api/messages/suggested-users',        [new MessagesController(), 'getSuggestedUsers']);

// ══════════════════════════════════════════════════════════════
//  MISC
// ══════════════════════════════════════════════════════════════
Flight::route('GET /hello-world/@name', function (string $name) {
    echo '<h1>Hello world! Oh hey ' . htmlspecialchars($name) . '!</h1>';
});
