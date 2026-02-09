-- SQL migration for messages feature
-- Run this against your MySQL/Postgres DB if you're not using the default SQLite fallback.
-- SQL migration for messages feature (MySQL)
-- Run this against your MySQL DB. It is written for MySQL/MariaDB.

-- Create database if missing and set utf8mb4
CREATE DATABASE IF NOT EXISTS base;
USE base;

-- Users table with firstName and lastName (compatible with forms.php)
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  firstName VARCHAR(255) NOT NULL,
  lastName VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  KEY idx_users_email (email)
);

-- Messages table
CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  sender_id INT UNSIGNED NOT NULL,
  recipient_id INT UNSIGNED NOT NULL,
  content TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_messages_sender (sender_id),
  INDEX idx_messages_recipient (recipient_id),
  CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_messages_recipient FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO users (firstName, lastName, email) VALUES
  ('Alice', 'Dupont', 'alice@gmail.com'),
  ('Bob', 'Martin', 'bob@gmail.com');

-- Insert sample messages using email lookups so IDs match existing users
INSERT INTO `messages` (`sender_id`, `recipient_id`, `content`)
SELECT
  (SELECT id FROM `users` WHERE email = 'alice@gmail.com' LIMIT 1) AS sender,
  (SELECT id FROM `users` WHERE email = 'bob@gmail.com' LIMIT 1) AS recipient,
  'Bonjour Bob ! Ceci est un message de test de Alice vers Bob.'
WHERE
  (SELECT id FROM `users` WHERE email = 'alice@gmail.com' LIMIT 1) IS NOT NULL
  AND (SELECT id FROM `users` WHERE email = 'bob@gmail.com' LIMIT 1) IS NOT NULL;

INSERT INTO `messages` (`sender_id`, `recipient_id`, `content`)
SELECT
  (SELECT id FROM `users` WHERE email = 'bob@gmail.com' LIMIT 1) AS sender,
  (SELECT id FROM `users` WHERE email = 'alice@gmail.com' LIMIT 1) AS recipient,
  'Salut Alice ! Réponse de Bob.'
WHERE
  (SELECT id FROM `users` WHERE email = 'bob@gmail.com' LIMIT 1) IS NOT NULL
  AND (SELECT id FROM `users` WHERE email = 'alice@gmail.com' LIMIT 1) IS NOT NULL;