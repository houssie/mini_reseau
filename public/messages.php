<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages & Communication - Modern Bootstrap Admin</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Real-time messaging and communication center with chat interface">
    <meta name="keywords" content="bootstrap, admin, dashboard, messages, chat, communication">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="./assets/favicon-CvUZKS4z.svg">
    <link rel="icon" type="image/png" href="./assets/favicon-B_cwPWBd.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="./assets/manifest-DTaoG9pG.json">
    
    <!-- Preload critical fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script type="module" crossorigin src="./assets/vendor-bootstrap-C9iorZI5.js"></script>
    <script type="module" crossorigin src="./assets/vendor-charts-DGwYAWel.js"></script>
    <script type="module" crossorigin src="./assets/vendor-ui-CflGdlft.js"></script>
    <script type="module" crossorigin src="./assets/main-DwHigVru.js"></script>
    <script type="module" crossorigin src="./assets/messages-ByGNYy7N.js"></script>
    <link rel="stylesheet" crossorigin href="./assets/main-QD_VOj1Y.css">
    <style>
        .unread-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 10px;
            background-color: #dc3545;
            color: white;
            font-size: 11px;
            font-weight: 600;
        }
        .message-status {
            margin-left: 5px;
            font-size: 12px;
        }
        .message-status.read {
            color: #28a745;
        }
        .message-status.unread {
            color: #6c757d;
        }
        .conversation-item.unread {
            background-color: rgba(13, 110, 253, 0.05);
            border-left: 3px solid #0d6efd;
        }
        .refresh-btn {
            transition: transform 0.3s ease;
        }
        .refresh-btn.spinning {
            animation: spin 1s linear;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .avatar-placeholder {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
        .typing-indicator {
            display: none;
        }
        .typing-indicator.active {
            display: flex;
        }
    </style>
</head>

<body data-page="messages" class="messages-page">
    
    <!-- Admin App Container -->
    <div class="admin-app">
        <div class="admin-wrapper" id="admin-wrapper">
            
            <?php include '../app/includes/header.php'; ?>

            <?php include '../app/includes/sidebar.php'; ?>

            <!-- Floating Hamburger Menu -->
            <button class="hamburger-menu" 
                    type="button" 
                    data-sidebar-toggle
                    aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>

            <!-- Main Content -->
            <main class="admin-main">
                <div class="container-fluid p-4 p-lg-4">
                    
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h1 class="h3 mb-0">Messages</h1>
                            <p class="text-muted mb-0">Real-time communication center</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary d-lg-none" id="toggleConversations">
                                <i class="bi bi-list me-2"></i>Conversations
                            </button>
                            <button type="button" class="btn btn-outline-primary refresh-btn" id="refreshMessages" title="Rafraîchir les messages">
                                <i class="bi bi-arrow-clockwise me-2"></i>Rafraîchir
                            </button>
                            <?php if ($selected_user): ?>
                            <button type="button" class="btn btn-outline-secondary btn-mark-read" data-user-id="<?php echo $selected_user['id']; ?>">
                                <i class="bi bi-check-all me-2"></i>Mark as Read
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                                <i class="bi bi-plus-lg me-2"></i>New Message
                            </button>
                        </div>
                    </div>

                    <!-- Messages Container -->
                    <div class="messages-container">
                        <div class="messages-layout">
                            
                            <!-- Conversations Sidebar -->
                            <div class="messages-sidebar" id="conversationsSidebar">
                                <!-- Sidebar Header -->
                                <div class="messages-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="header-title mb-0">Conversations</h5>
                                        <button type="button" class="btn btn-sm btn-outline-secondary refresh-btn" id="sidebarRefresh" title="Refresh">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex gap-2 mt-3">
                                        <div class="search-container flex-grow-1">
                                            <input type="search" 
                                                   class="form-control" 
                                                   placeholder="Search conversations..."
                                                   id="searchConversations">
                                            <i class="bi bi-search search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Conversations List -->
                                <div class="conversations-list">
                                    <?php if (empty($conversations)): ?>
                                    <div class="empty-conversations">
                                        <i class="bi bi-chat-dots text-muted"></i>
                                        <p>No conversations yet</p>
                                        <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                                            Start a conversation
                                        </button>
                                    </div>
                                    <?php else: ?>
                                        <?php foreach ($conversations as $conversation): ?>
                                        <a href="?conversation=<?php echo $conversation['user_id']; ?>" 
                                           class="conversation-item <?php echo ($selected_conversation_id == $conversation['user_id']) ? 'active' : ''; ?> <?php echo ($conversation['unread_count'] > 0) ? 'unread' : ''; ?>">
                                            <div class="conversation-avatar">
                                                <div class="avatar-placeholder bg-primary text-white rounded-circle">
                                                    <span><?php echo getInitial($conversation['user_name']); ?></span>
                                                </div>
                                            </div>
                                            <div class="conversation-info">
                                                <div class="conversation-header">
                                                    <h6 class="conversation-name"><?php echo htmlspecialchars($conversation['user_name']); ?></h6>
                                                    <span class="conversation-time"><?php echo $conversation['last_message_display']; ?></span>
                                                </div>
                                                <p class="conversation-preview"><?php echo htmlspecialchars($conversation['last_message'] ?? 'No messages yet'); ?></p>
                                                <div class="conversation-footer">
                                                    <span class="conversation-type"><?php echo htmlspecialchars($conversation['user_email']); ?></span>
                                                    <?php if ($conversation['unread_count'] > 0): ?>
                                                    <span class="unread-badge"><?php echo $conversation['unread_count']; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Chat Area -->
                            <div class="chat-area">
                                <?php if ($selected_user): ?>
                                <!-- Active Chat -->
                                <div class="active-chat">
                                    <!-- Chat Header -->
                                    <div class="chat-header">
                                        <div class="chat-user-info">
                                            <button class="btn btn-link d-lg-none me-2 p-0" id="mobileBackButton">
                                                <i class="bi bi-arrow-left fs-5"></i>
                                            </button>
                                            <div class="chat-avatar-container">
                                                <div class="avatar-placeholder bg-primary text-white rounded-circle" style="width: 40px; height: 40px;">
                                                    <span><?php echo getInitial($selected_user['name']); ?></span>
                                                </div>
                                            </div>
                                            <div class="chat-details">
                                                <h6 class="chat-name"><?php echo htmlspecialchars($selected_user['name']); ?></h6>
                                                <p class="chat-status"><?php echo htmlspecialchars($selected_user['email']); ?></p>
                                            </div>
                                        </div>
                                        <div class="chat-actions">
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-mark-read" data-user-id="<?php echo $selected_user['id']; ?>" title="Mark as read">
                                                <i class="bi bi-check-all"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Messages Area -->
                                    <div class="chat-messages" id="chatMessages">
                                        <?php if (empty($conversation_messages)): ?>
                                        <div class="text-center py-5 text-muted">
                                            <i class="bi bi-chat-dots fs-1"></i>
                                            <p class="mt-2">No messages yet. Start the conversation!</p>
                                        </div>
                                        <?php else: ?>
                                            <?php foreach ($conversation_messages as $message): ?>
                                            <div class="message <?php echo ($message['sender_id'] == $current_user_id) ? 'own-message' : ''; ?>">
                                                <?php if ($message['sender_id'] != $current_user_id): ?>
                                                <div class="avatar-placeholder bg-primary text-white rounded-circle" style="width: 32px; height: 32px; font-size: 14px; margin-right: 10px;">
                                                    <span><?php echo getInitial($selected_user['name']); ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <div class="message-bubble">
                                                    <div class="message-content">
                                                        <p><?php echo htmlspecialchars($message['content']); ?></p>
                                                    </div>
                                                    <div class="message-info">
                                                        <span class="message-time"><?php echo formatTime($message['created_at']); ?></span>
                                                        <?php if ($message['sender_id'] == $current_user_id): ?>
                                                        <span class="message-status <?php echo ($message['is_read'] == 1) ? 'read' : 'unread'; ?>">
                                                            <?php if ($message['is_read'] == 1): ?>
                                                            <i class="bi bi-check-all"></i>
                                                            <?php else: ?>
                                                            <i class="bi bi-check"></i>
                                                            <?php endif; ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Message Input -->
                                    <div class="chat-input">
                                        <script>
                                        async function sendMsg() {
                                            var t = document.getElementById('messageInput');
                                            var c = t.value.trim();
                                            var r = document.getElementById('recipientId').value;
                                            if (!c) return alert('Veuillez entrer un message');
                                            
                                            var b = document.getElementById('sendButton');
                                            b.disabled = true;
                                            
                                            var f = new FormData();
                                            f.append('action', 'send_message');
                                            f.append('recipient_id', r);
                                            f.append('content', c);
                                            
                                            try {
                                                var res = await fetch('/messages', {
                                                    method: 'POST',
                                                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                                                    body: f
                                                });
                                                var j = await res.json();
                                                if (j.success) {
                                                    var m = document.getElementById('chatMessages');
                                                    var h = '<div class="message own-message"><div class="message-bubble"><div class="message-content"><p>' + c.replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</p></div><div class="message-info"><span class="message-time">' + new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}) + '</span></div></div></div>';
                                                    var e = m.querySelector('.text-center.py-5');
                                                    if (e) m.innerHTML = h; else m.insertAdjacentHTML('beforeend', h);
                                                    m.scrollTop = m.scrollHeight;
                                                    t.value = '';
                                                } else alert(j.error || 'Erreur');
                                            } catch(err) { alert('Erreur: ' + err.message); }
                                            b.disabled = false;
                                        }
                                        </script>
                                        <form id="messageForm" onsubmit="return false;">
                                            <input type="hidden" id="recipientId" value="<?php echo $selected_user['id']; ?>">
                                            <div class="input-container">
                                                <div class="message-input">
                                                    <textarea class="form-control" id="messageInput" placeholder="Type a message..." rows="1" onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMsg();}" style="resize:none;"></textarea>
                                                </div>
                                                <div class="input-actions">
                                                    <button type="button" class="btn btn-primary" id="sendButton" onclick="sendMsg()"><i class="bi bi-send"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <?php else: ?>
                                <!-- Empty Chat State -->
                                <div class="empty-chat">
                                    <div class="empty-icon">
                                        <i class="bi bi-chat-dots"></i>
                                    </div>
                                    <h5 class="empty-text">Select a conversation to start messaging</h5>
                                    <p class="text-muted mb-4">Choose from your existing conversations or start a new one</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                                        <i class="bi bi-plus-lg me-2"></i>Start New Conversation
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>

                </div>
            </main>

            <?php include '../app/includes/footer.php'; ?>


    <!-- New Message Modal -->
    <div class="modal fade" id="newMessageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="/messages">
                    <input type="hidden" name="action" value="send_message">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Recipient</label>
                            <select class="form-select" name="recipient_id" required>
                                <option value="">Choose a user...</option>
                                <?php if (!empty($all_users)): ?>
                                    <?php foreach ($all_users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No users found in database</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="content" rows="4" placeholder="Type your message here..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Debug Information (à retirer en production) -->
    <?php if (isset($_GET['debug'])): ?>
    <div style="position: fixed; bottom: 10px; right: 10px; background: white; padding: 10px; border: 1px solid #ccc; z-index: 1000; max-width: 400px; font-size: 12px;">
        <strong>Debug Info:</strong><br>
        Current User ID: <?php echo $current_user_id; ?><br>
        Current User Name: <?php echo $current_user_name; ?><br>
        Total Users in DB: <?php echo count($all_users); ?><br>
        Selected Conversation: <?php echo $selected_conversation_id; ?><br>
        Conversations count: <?php echo count($conversations); ?><br>
        All Users:<br>
        <ul>
            <?php foreach ($all_users as $user): ?>
            <li>ID: <?php echo $user['id']; ?> - <?php echo $user['name']; ?> (<?php echo $user['email']; ?>)</li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll en bas des messages
        var c = document.getElementById('chatMessages');
        if (c) c.scrollTop = c.scrollHeight;
        
        // Focus sur input
        var i = document.getElementById('messageInput');
        if (i) i.focus();
        
        // Toggle mobile sidebar
        var t = document.getElementById('toggleConversations');
        var b = document.getElementById('mobileBackButton');
        var s = document.getElementById('conversationsSidebar');
        if (t && s) t.onclick = function() { s.classList.toggle('mobile-show'); };
        if (b && s) b.onclick = function() { s.classList.toggle('mobile-show'); };

        // ── Helpers ──
        function esc(s){ return s ? String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;') : ''; }
        function initial(n){ return n ? n.trim().charAt(0).toUpperCase() : '?'; }
        function selectedId(){
            try { return new URLSearchParams(location.search).get('conversation') || ''; }
            catch(e){ return ''; }
        }

        // ── AJAX: Refresh conversations list ──
        async function refreshConversations(btn){
            if (btn) btn.classList.add('spinning');
            try {
                var res = await fetch('/api/messages/conversations', { credentials:'same-origin' });
                if (!res.ok) throw new Error(res.status);
                var data = await res.json();
                var list = document.querySelector('.conversations-list');
                if (!list || !data.conversations) return;

                if (data.conversations.length === 0) {
                    list.innerHTML = '<div class="empty-conversations"><i class="bi bi-chat-dots text-muted"></i><p>No conversations yet</p><button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newMessageModal">Start a conversation</button></div>';
                    return;
                }

                var sel = selectedId();
                var html = '';
                data.conversations.forEach(function(cv){
                    var active  = (String(cv.user_id) === sel) ? 'active' : '';
                    var unread  = (cv.unread_count > 0) ? 'unread' : '';
                    var badge   = (cv.unread_count > 0) ? '<span class="unread-badge">' + cv.unread_count + '</span>' : '';
                    html += '<a href="?conversation=' + cv.user_id + '" class="conversation-item ' + active + ' ' + unread + '">'
                        + '<div class="conversation-avatar"><div class="avatar-placeholder bg-primary text-white rounded-circle"><span>' + initial(cv.user_name) + '</span></div></div>'
                        + '<div class="conversation-info">'
                        + '<div class="conversation-header"><h6 class="conversation-name">' + esc(cv.user_name) + '</h6><span class="conversation-time">' + esc(cv.last_message_display || '') + '</span></div>'
                        + '<p class="conversation-preview">' + esc(cv.last_message || 'No messages yet') + '</p>'
                        + '<div class="conversation-footer"><span class="conversation-type">' + esc(cv.user_email) + '</span>' + badge + '</div>'
                        + '</div></a>';
                });
                list.innerHTML = html;
            } catch(err) {
                console.error('Refresh failed:', err);
            } finally {
                if (btn) btn.classList.remove('spinning');
            }
        }

        // ── AJAX: Mark conversation as read ──
        async function markRead(otherUserId, btn){
            if (!otherUserId) return;
            if (btn) btn.disabled = true;
            try {
                var res = await fetch('/api/messages/mark-conversation-read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({ other_user_id: parseInt(otherUserId) })
                });
                var json = await res.json();
                if (json.success) {
                    // Remove unread badge from the active conversation in sidebar
                    var activeConv = document.querySelector('.conversation-item.active');
                    if (activeConv) {
                        activeConv.classList.remove('unread');
                        var badge = activeConv.querySelector('.unread-badge');
                        if (badge) badge.remove();
                    }
                    // Also remove badge from any conversation matching this user
                    document.querySelectorAll('.conversation-item').forEach(function(el){
                        var href = el.getAttribute('href') || '';
                        if (href.indexOf('conversation=' + otherUserId) !== -1) {
                            el.classList.remove('unread');
                            var b = el.querySelector('.unread-badge');
                            if (b) b.remove();
                        }
                    });
                    // Refresh sidebar to fully sync with server
                    refreshConversations(null);
                } else {
                    alert(json.error || 'Erreur');
                }
            } catch(err) {
                console.error('Mark-read failed:', err);
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        // ── Bind: Refresh buttons ──
        var refreshMain = document.getElementById('refreshMessages');
        if (refreshMain) refreshMain.addEventListener('click', function(){ refreshConversations(this); });

        var refreshSide = document.getElementById('sidebarRefresh');
        if (refreshSide) refreshSide.addEventListener('click', function(){ refreshConversations(this); });

        // ── Bind: Mark-as-Read buttons ──
        document.querySelectorAll('.btn-mark-read').forEach(function(btn){
            btn.addEventListener('click', function(){
                markRead(this.dataset.userId, this);
            });
        });
    });
    </script>
</body>
</html>