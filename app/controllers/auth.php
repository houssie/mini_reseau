<?php

/**
 * Login by email only (auto-login, no password).
 * Looks up the user by email. Returns the user array or false.
 */
function login_by_email(PDO $pdo, string $email) {
    $email = trim($email);
    if ($email === '') return false;

    $stmt = $pdo->prepare('SELECT id, firstName, lastName, email FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) return false;

    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = trim($user['firstName'] . ' ' . $user['lastName']);
    $_SESSION['user_email'] = $user['email'];

    return $user;
}

/**
 * Auto-login: find user by email, or create a new one with firstName/lastName/email,
 * then start a session. Returns the user array or false on failure.
 */
function auto_login_or_create(PDO $pdo, string $firstName, string $lastName, string $email) {
    $firstName = trim($firstName);
    $lastName  = trim($lastName);
    $email     = trim($email);

    if ($email === '' || $firstName === '' || $lastName === '') {
        return false;
    }

    // Try to find existing user
    $stmt = $pdo->prepare('SELECT id, firstName, lastName, email FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Create the user
        $insert = $pdo->prepare(
            'INSERT INTO users (firstName, lastName, email) VALUES (:firstName, :lastName, :email)'
        );
        $insert->execute([
            ':firstName' => $firstName,
            ':lastName'  => $lastName,
            ':email'     => $email,
        ]);

        $user = [
            'id'        => (int) $pdo->lastInsertId(),
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'email'     => $email,
        ];
    }

    // Start session
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = trim($user['firstName'] . ' ' . $user['lastName']);
    $_SESSION['user_email'] = $user['email'];

    return $user;
}
