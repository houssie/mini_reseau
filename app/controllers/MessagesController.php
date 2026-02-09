<?php

require_once __DIR__ . '/../models/MessageModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/auth.php';

class MessagesController
{
    private PDO $pdo;
    private MessageModel $messages;
    private UserModel $users;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo      = $pdo ?? Flight::get('pdo');
        $this->messages  = new MessageModel($this->pdo);
        $this->users     = new UserModel($this->pdo);
    }

    private function json(array $data, int $code = 200): void
    {
        Flight::json($data, $code);
    }

    public function loginAuto() {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $email = trim((string)($input['email'] ?? ''));
        if ($email === '') {
            return $this->json(['error' => 'email required'], 400);
        }

        try {
            $user = login_by_email($this->pdo, $email);
        } catch (Exception $e) {
            return $this->json(['error' => 'internal error'], 500);
        }

        if (!$user) return $this->json(['error' => 'User not found'], 404);

        $this->json(['success' => true, 'user' => $user]);
    }

    public function listUsers() {
        $users = $this->users->listAll();
        $this->json(['users' => $users]);
    }

    public function sendMessage() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $from = $_SESSION['user_id'] ?? null;
        if (!$from) return $this->json(['error' => 'Not authenticated'], 401);

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $to = $input['recipient_id'] ?? null;
        $content = trim($input['content'] ?? '');
        if (!$to || $content === '') return $this->json(['error' => 'recipient_id and content required'], 400);

        $id = $this->messages->create((int)$from, (int)$to, $content);
        $this->json(['success' => true, 'message_id' => $id]);
    }

    /**
     * Envoi direct de message (pour les formulaires HTML)
     */
    public function sendMessageDirect(int $fromUserId, int $toUserId, string $content): int {
        return $this->messages->create($fromUserId, $toUserId, $content);
    }

    /**
     * Marquer une conversation comme lue (pour les formulaires HTML)
     */
    public function markConversationReadDirect(int $userId, int $otherUserId): int {
        return $this->messages->markConversationAsRead($userId, $otherUserId);
    }

    public function fetchConversation() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        $other = $_GET['with'] ?? null;
        $since = $_GET['since'] ?? null;
        if (!$other) return $this->json(['error' => 'with parameter required'], 400);

        $msgs = $this->messages->fetchBetween((int)$me, (int)$other, $since);
        $this->json(['messages' => $msgs]);
    }

    public function fetchForUser() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        $since = $_GET['since'] ?? null;
        $msgs = $this->messages->fetchForUser((int)$me, $since);
        $this->json(['messages' => $msgs]);
    }

    public function markRead() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $ids = $input['message_ids'] ?? [];
        if (!is_array($ids)) return $this->json(['error' => 'message_ids must be array'], 400);

        $updated = $this->messages->markAsRead($ids);
        $this->json(['success' => true, 'updated' => $updated]);
    }

    // NOUVELLE METHODE: Récupérer toutes les conversations
    public function getConversations() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        try {
            // Récupérer toutes les conversations avec les derniers messages
            $sql = '
                SELECT 
                    u.id as user_id,
                    CONCAT(u.firstName, \' \', u.lastName) as user_name,
                    u.email as user_email,
                    MAX(m.created_at) as last_message_time,
                    (
                        SELECT m2.content 
                        FROM messages m2 
                        WHERE (m2.sender_id = u.id AND m2.recipient_id = ?) 
                           OR (m2.sender_id = ? AND m2.recipient_id = u.id)
                        ORDER BY m2.created_at DESC 
                        LIMIT 1
                    ) as last_message,
                    (
                        SELECT COUNT(*) 
                        FROM messages m3 
                        WHERE m3.sender_id = u.id 
                          AND m3.recipient_id = ? 
                          AND m3.is_read = 0
                    ) as unread_count
                FROM users u
                LEFT JOIN messages m ON (
                    (m.sender_id = u.id AND m.recipient_id = ?) 
                    OR (m.sender_id = ? AND m.recipient_id = u.id)
                )
                WHERE u.id != ?
                GROUP BY u.id, u.firstName, u.lastName, u.email
                ORDER BY (MAX(m.created_at) IS NULL) ASC, MAX(m.created_at) DESC, u.firstName
            ';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$me, $me, $me, $me, $me, $me]);
            $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Formater les résultats
            foreach ($conversations as &$conv) {
                $conv['user_id'] = (int)$conv['user_id'];
                $conv['unread_count'] = (int)$conv['unread_count'];
                
                // Si pas de dernier message, mettre des valeurs par défaut
                if (!$conv['last_message']) {
                    $conv['last_message'] = 'Aucun message';
                    $conv['last_message_time'] = null;
                }
                
                // Formater la date du dernier message
                if ($conv['last_message_time']) {
                    $date = new DateTime($conv['last_message_time']);
                    $now = new DateTime();
                    $interval = $now->diff($date);
                    
                    if ($interval->days == 0) {
                        $conv['last_message_display'] = $date->format('H:i');
                    } elseif ($interval->days == 1) {
                        $conv['last_message_display'] = 'Hier';
                    } elseif ($interval->days < 7) {
                        $conv['last_message_display'] = $date->format('D');
                    } else {
                        $conv['last_message_display'] = $date->format('d/m/Y');
                    }
                } else {
                    $conv['last_message_display'] = '';
                }
                
                // Tronquer le dernier message si trop long
                if (strlen($conv['last_message']) > 50) {
                    $conv['last_message'] = substr($conv['last_message'], 0, 47) . '...';
                }
            }

            $this->json(['conversations' => $conversations]);

        } catch (Exception $e) {
            error_log('Error in getConversations: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

    // NOUVELLE METHODE: Récupérer les statistiques des messages
    public function getMessageStats() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        try {
            // Total des messages non lus
            $stmt1 = $this->pdo->prepare('
                SELECT COUNT(*) as total_unread 
                FROM messages 
                WHERE recipient_id = ? AND is_read = 0
            ');
            $stmt1->execute([$me]);
            $unread = $stmt1->fetch(PDO::FETCH_ASSOC);

            // Total des conversations
            $stmt2 = $this->pdo->prepare('
                SELECT COUNT(DISTINCT 
                    CASE 
                        WHEN sender_id = ? THEN recipient_id 
                        ELSE sender_id 
                    END
                ) as total_conversations
                FROM messages 
                WHERE ? IN (sender_id, recipient_id)
            ');
            $stmt2->execute([$me, $me]);
            $conversations = $stmt2->fetch(PDO::FETCH_ASSOC);

            // Dernier message reçu
            $stmt3 = $this->pdo->prepare('
                SELECT m.*, CONCAT(u.firstName, \' \', u.lastName) as sender_name 
                FROM messages m
                JOIN users u ON u.id = m.sender_id
                WHERE m.recipient_id = ?
                ORDER BY m.created_at DESC 
                LIMIT 1
            ');
            $stmt3->execute([$me]);
            $lastMessage = $stmt3->fetch(PDO::FETCH_ASSOC);

            $this->json([
                'stats' => [
                    'total_unread' => (int)($unread['total_unread'] ?? 0),
                    'total_conversations' => (int)($conversations['total_conversations'] ?? 0),
                    'last_message' => $lastMessage
                ]
            ]);

        } catch (Exception $e) {
            error_log('Error in getMessageStats: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

    // NOUVELLE METHODE: Rechercher dans les messages
    public function searchMessages() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            return $this->json(['error' => 'Search query too short'], 400);
        }

        try {
            $searchQuery = '%' . $query . '%';
            
            $sql = '
                SELECT m.*, 
                       CONCAT(u1.firstName, \' \', u1.lastName) as sender_name,
                       CONCAT(u2.firstName, \' \', u2.lastName) as recipient_name
                FROM messages m
                JOIN users u1 ON u1.id = m.sender_id
                JOIN users u2 ON u2.id = m.recipient_id
                WHERE (m.sender_id = ? OR m.recipient_id = ?)
                  AND m.content LIKE ?
                ORDER BY m.created_at DESC
                LIMIT 50
            ';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$me, $me, $searchQuery]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->json(['results' => $results, 'count' => count($results)]);

        } catch (Exception $e) {
            error_log('Error in searchMessages: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

    // NOUVELLE METHODE: Marquer toute une conversation comme lue
    public function markConversationRead() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $otherUserId = $input['other_user_id'] ?? null;
        
        if (!$otherUserId) {
            return $this->json(['error' => 'other_user_id required'], 400);
        }

        try {
            $sql = '
                UPDATE messages 
                SET is_read = 1 
                WHERE sender_id = ? 
                  AND recipient_id = ? 
                  AND is_read = 0
            ';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$otherUserId, $me]);
            $updated = $stmt->rowCount();

            $this->json([
                'success' => true, 
                'updated' => $updated,
                'message' => "{$updated} message(s) marqué(s) comme lu"
            ]);

        } catch (Exception $e) {
            error_log('Error in markConversationRead: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

    // NOUVELLE METHODE: Récupérer les messages depuis une date
    public function getMessagesSince() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        $since = $_GET['since'] ?? null;
        if (!$since) {
            return $this->json(['error' => 'since parameter required'], 400);
        }

        try {
            // Valider le format de la date
            if (!strtotime($since)) {
                return $this->json(['error' => 'Invalid date format'], 400);
            }

            $sql = '
                SELECT m.*, CONCAT(u.firstName, \' \', u.lastName) as sender_name
                FROM messages m
                JOIN users u ON u.id = m.sender_id
                WHERE (m.sender_id = ? OR m.recipient_id = ?)
                  AND m.created_at > ?
                ORDER BY m.created_at ASC
            ';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$me, $me, $since]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->json([
                'messages' => $messages,
                'count' => count($messages),
                'since' => $since
            ]);

        } catch (Exception $e) {
            error_log('Error in getMessagesSince: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

    // NOUVELLE METHODE: Récupérer les utilisateurs suggérés (avec qui on n'a pas encore discuté)
    public function getSuggestedUsers() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        try {
            $sql = '
                SELECT u.id, CONCAT(u.firstName, \' \', u.lastName) as name, u.email
                FROM users u
                WHERE u.id != ?
                  AND u.id NOT IN (
                      SELECT DISTINCT 
                          CASE 
                              WHEN sender_id = ? THEN recipient_id 
                              ELSE sender_id 
                          END
                      FROM messages 
                      WHERE ? IN (sender_id, recipient_id)
                  )
                ORDER BY u.firstName
                LIMIT 10
            ';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$me, $me, $me]);
            $suggestedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->json(['suggested_users' => $suggestedUsers]);

        } catch (Exception $e) {
            error_log('Error in getSuggestedUsers: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

    // NOUVELLE METHODE: Récupérer le nombre de messages non lus par utilisateur
    public function getUnreadCounts() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        try {
            $sql = '
                SELECT 
                    u.id as user_id,
                    CONCAT(u.firstName, \' \', u.lastName) as user_name,
                    COUNT(m.id) as unread_count
                FROM users u
                JOIN messages m ON m.sender_id = u.id AND m.recipient_id = ? AND m.is_read = 0
                WHERE u.id != ?
                GROUP BY u.id, u.firstName, u.lastName
                ORDER BY unread_count DESC
            ';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$me, $me]);
            $unreadCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Ajouter les utilisateurs avec 0 message non lu
            $allUsers = $this->users->listAll();
            $result = [];
            
            foreach ($allUsers as $user) {
                if ($user['id'] == $me) continue;
                
                $found = false;
                foreach ($unreadCounts as $count) {
                    if ($count['user_id'] == $user['id']) {
                        $result[] = $count;
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $result[] = [
                        'user_id' => $user['id'],
                        'user_name' => $user['name'],
                        'unread_count' => 0
                    ];
                }
            }

            $this->json(['unread_counts' => $result]);

        } catch (Exception $e) {
            error_log('Error in getUnreadCounts: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

    // Méthodes utilitaires pour la vue
    public static function formatTime($dateString) {
        if (!$dateString) return '';
        
        $date = new DateTime($dateString);
        $now = new DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            // Aujourd'hui, montrer seulement l'heure
            return $date->format('H:i');
        } elseif ($diff->days == 1) {
            // Hier
            return 'Yesterday ' . $date->format('H:i');
        } elseif ($diff->days < 7) {
            // Cette semaine
            return $date->format('D H:i');
        } else {
            // Plus d'une semaine
            return $date->format('d/m/Y H:i');
        }
    }
    
    public static function getInitial($name) {
        return $name ? strtoupper(substr($name, 0, 1)) : '?';
    }

    // NOUVELLE METHODE: Préparer les données pour la page messages.php
    public function prepareMessagesPageData() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (empty($_SESSION['user_id'])) {
            header('Location: /login.php');
            exit;
        }

        $current_user_id = $_SESSION['user_id'];
        $current_user_name = $_SESSION['user_name'] ?? 'Utilisateur';
        $current_user_email = $_SESSION['user_email'] ?? '';

        try {
            // Récupérer les informations utilisateur actuelles
            $stmt = $this->pdo->prepare('SELECT id, CONCAT(firstName, \' \', lastName) as name, firstName, lastName, email FROM users WHERE id = ?');
            $stmt->execute([$current_user_id]);
            $current_user = $stmt->fetch();

            if ($current_user) {
                $current_user_name = $current_user['name'];
                $current_user_email = $current_user['email'];
                $_SESSION['user_name'] = $current_user_name;
                $_SESSION['user_email'] = $current_user_email;
            }

            // Gérer l'envoi de message
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                if ($_POST['action'] === 'send_message' && isset($_POST['recipient_id'], $_POST['content'])) {
                    $recipient_id = (int)$_POST['recipient_id'];
                    $content = trim($_POST['content']);

                    if ($content !== '' && $recipient_id > 0) {
                        $stmt = $this->pdo->prepare('INSERT INTO messages (sender_id, recipient_id, content, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)');
                        $stmt->execute([$current_user_id, $recipient_id, $content]);

                        // Rediriger vers la conversation
                        header('Location: messages.php?conversation=' . $recipient_id);
                        exit;
                    }
                }

                if ($_POST['action'] === 'mark_read' && isset($_POST['message_ids'])) {
                    $message_ids = $_POST['message_ids'];
                    if (is_array($message_ids) && !empty($message_ids)) {
                        $placeholders = implode(',', array_fill(0, count($message_ids), '?'));
                        $stmt = $this->pdo->prepare("UPDATE messages SET is_read = 1 WHERE id IN ($placeholders)");
                        $stmt->execute($message_ids);
                    }
                }

                if ($_POST['action'] === 'mark_conversation_read' && isset($_POST['other_user_id'])) {
                    $other_user_id = (int)$_POST['other_user_id'];
                    $stmt = $this->pdo->prepare('UPDATE messages SET is_read = 1 WHERE sender_id = ? AND recipient_id = ? AND is_read = 0');
                    $stmt->execute([$other_user_id, $current_user_id]);
                    // Rediriger pour rafraîchir la page
                    header('Location: messages.php?conversation=' . $other_user_id);
                    exit;
                }
            }

            // Récupérer la conversation sélectionnée
            $selected_conversation_id = isset($_GET['conversation']) ? (int)$_GET['conversation'] : 0;
            $selected_user = null;
            $conversation_messages = [];

            if ($selected_conversation_id > 0) {
                // Récupérer les informations de l'utilisateur sélectionné
                $stmt = $this->pdo->prepare('SELECT id, CONCAT(firstName, \' \', lastName) as name, email FROM users WHERE id = ?');
                $stmt->execute([$selected_conversation_id]);
                $selected_user = $stmt->fetch();

                if ($selected_user) {
                    // Récupérer les messages de la conversation
                    $stmt = $this->pdo->prepare('
                        SELECT m.*, CONCAT(u.firstName, \' \', u.lastName) as sender_name
                        FROM messages m
                        LEFT JOIN users u ON u.id = m.sender_id
                        WHERE (m.sender_id = ? AND m.recipient_id = ?)
                           OR (m.sender_id = ? AND m.recipient_id = ?)
                        ORDER BY m.created_at ASC
                    ');
                    $stmt->execute([
                        $current_user_id,
                        $selected_conversation_id,
                        $selected_conversation_id,
                        $current_user_id
                    ]);
                    $conversation_messages = $stmt->fetchAll();

                    // Marquer les messages comme lus
                    $stmt = $this->pdo->prepare('UPDATE messages SET is_read = 1 WHERE sender_id = ? AND recipient_id = ? AND is_read = 0');
                    $stmt->execute([$selected_conversation_id, $current_user_id]);
                }
            }

            // Récupérer la liste des conversations (utilisateurs avec qui on a déjà parlé)
            $stmt = $this->pdo->prepare('
                SELECT DISTINCT
                    u.id as user_id,
                    CONCAT(u.firstName, \' \', u.lastName) as user_name,
                    u.email as user_email,
                    (
                        SELECT m2.content
                        FROM messages m2
                        WHERE (m2.sender_id = u.id AND m2.recipient_id = ?)
                           OR (m2.sender_id = ? AND m2.recipient_id = u.id)
                        ORDER BY m2.created_at DESC
                        LIMIT 1
                    ) as last_message,
                    (
                        SELECT m2.created_at
                        FROM messages m2
                        WHERE (m2.sender_id = u.id AND m2.recipient_id = ?)
                           OR (m2.sender_id = ? AND m2.recipient_id = u.id)
                        ORDER BY m2.created_at DESC
                        LIMIT 1
                    ) as last_message_time,
                    (
                        SELECT COUNT(*)
                        FROM messages m3
                        WHERE m3.sender_id = u.id
                          AND m3.recipient_id = ?
                          AND m3.is_read = 0
                    ) as unread_count
                FROM users u
                INNER JOIN messages m ON (
                    (m.sender_id = u.id AND m.recipient_id = ?)
                    OR (m.sender_id = ? AND m.recipient_id = u.id)
                )
                WHERE u.id != ?
                GROUP BY u.id, u.firstName, u.lastName, u.email
                ORDER BY (MAX(m.created_at) IS NULL) ASC, MAX(m.created_at) DESC, u.firstName
            ');
            $stmt->execute([
                $current_user_id, $current_user_id,
                $current_user_id, $current_user_id,
                $current_user_id,
                $current_user_id, $current_user_id,
                $current_user_id
            ]);
            $conversations = $stmt->fetchAll();

            // Formater les dates des conversations
            foreach ($conversations as &$conv) {
                $conv['unread_count'] = (int)$conv['unread_count'];

                if ($conv['last_message_time']) {
                    $date = new DateTime($conv['last_message_time']);
                    $now = new DateTime();
                    $interval = $now->diff($date);

                    if ($interval->days == 0) {
                        $conv['last_message_display'] = $date->format('H:i');
                    } elseif ($interval->days == 1) {
                        $conv['last_message_display'] = 'Yesterday';
                    } elseif ($interval->days < 7) {
                        $conv['last_message_display'] = $date->format('D');
                    } else {
                        $conv['last_message_display'] = $date->format('d/m/Y');
                    }
                } else {
                    $conv['last_message_display'] = '';
                }

                // Tronquer le dernier message si trop long
                if ($conv['last_message'] && strlen($conv['last_message']) > 50) {
                    $conv['last_message'] = substr($conv['last_message'], 0, 47) . '...';
                }
            }

            // Récupérer TOUS les utilisateurs sauf moi-même pour le modal
            $stmt = $this->pdo->prepare('SELECT id, CONCAT(firstName, \' \', lastName) as name, email FROM users WHERE id != ? ORDER BY firstName');
            $stmt->execute([$current_user_id]);
            $all_users = $stmt->fetchAll();

            // Si la table users est vide (ou il n'y a aucun autre utilisateur),
            // on tente d'insérer des utilisateurs de test (INSERT IGNORE) pour
            // aider en développement. Cela évite que le modal soit vide.
            if (empty($all_users)) {
                try {
                    $this->pdo->exec("INSERT IGNORE INTO users (firstName, lastName, email) VALUES ('Alice', 'Dupont', 'alice@gmail.com'), ('Bob', 'Martin', 'bob@gmail.com')");
                } catch (Exception $e) {
                    // Ne pas casser l'exécution en production — on ignore l'erreur
                    error_log('Seed users failed: ' . $e->getMessage());
                }

                // Recharger la liste après tentative d'insert
                $stmt->execute([$current_user_id]);
                $all_users = $stmt->fetchAll();
            }

            // Calculer le nombre total de messages non lus
            $stmt = $this->pdo->prepare('SELECT COUNT(*) as total_unread FROM messages WHERE recipient_id = ? AND is_read = 0');
            $stmt->execute([$current_user_id]);
            $unread_stats = $stmt->fetch();
            $total_unread = $unread_stats['total_unread'] ?? 0;

            return [
                'current_user_id' => $current_user_id,
                'current_user_name' => $current_user_name,
                'current_user_email' => $current_user_email,
                'selected_conversation_id' => $selected_conversation_id,
                'selected_user' => $selected_user,
                'conversation_messages' => $conversation_messages,
                'conversations' => $conversations,
                'all_users' => $all_users,
                'total_unread' => $total_unread
            ];

        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            return [
                'current_user_id' => $current_user_id,
                'current_user_name' => $current_user_name,
                'current_user_email' => $current_user_email,
                'selected_conversation_id' => 0,
                'selected_user' => null,
                'conversation_messages' => [],
                'conversations' => [],
                'all_users' => [],
                'total_unread' => 0
            ];
        }
    }

    // NOUVELLE METHODE: Récupérer les conversations récentes (avec pagination)
    public function getRecentConversations() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $me = $_SESSION['user_id'] ?? null;
        if (!$me) return $this->json(['error' => 'Not authenticated'], 401);

        $limit = min((int)($_GET['limit'] ?? 20), 100);
        $offset = max((int)($_GET['offset'] ?? 0), 0);

        try {
            $sql = '
                SELECT 
                    other_user.id as user_id,
                    CONCAT(other_user.firstName, \' \', other_user.lastName) as user_name,
                    other_user.email as user_email,
                    last_msg.content as last_message,
                    last_msg.created_at as last_message_time,
                    last_msg.is_read as last_message_read,
                    unread_count.count as unread_count
                FROM users other_user
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN m1.sender_id = ? THEN m1.recipient_id 
                            ELSE m1.sender_id 
                        END as other_id,
                        m1.content,
                        m1.created_at,
                        m1.is_read
                    FROM messages m1
                    WHERE ? IN (m1.sender_id, m1.recipient_id)
                      AND m1.created_at = (
                          SELECT MAX(m2.created_at)
                          FROM messages m2
                          WHERE (m2.sender_id = m1.sender_id AND m2.recipient_id = m1.recipient_id)
                             OR (m2.sender_id = m1.recipient_id AND m2.recipient_id = m1.sender_id)
                      )
                ) last_msg ON last_msg.other_id = other_user.id
                LEFT JOIN (
                    SELECT 
                        sender_id,
                        COUNT(*) as count
                    FROM messages
                    WHERE recipient_id = ? AND is_read = 0
                    GROUP BY sender_id
                ) unread_count ON unread_count.sender_id = other_user.id
                WHERE other_user.id != ?
                ORDER BY (last_msg.created_at IS NULL) ASC, last_msg.created_at DESC, other_user.firstName
                LIMIT ? OFFSET ?
            ';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$me, $me, $me, $me, $limit, $offset]);
            $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Compter le total
            $countSql = '
                SELECT COUNT(DISTINCT u.id) as total
                FROM users u
                WHERE u.id != ?
            ';
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute([$me]);
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

            $this->json([
                'conversations' => $conversations,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'total' => (int)$total,
                    'has_more' => ($offset + $limit) < $total
                ]
            ]);

        } catch (Exception $e) {
            error_log('Error in getRecentConversations: ' . $e->getMessage());
            $this->json(['error' => 'Database error'], 500);
        }
    }

}