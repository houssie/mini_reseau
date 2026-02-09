<?php

/**********************************************
 * FlightPHP Bootstrap File
 **********************************************/

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

// Load configuration
$config = require_once __DIR__ . '/config.php';

// Initialize FlightPHP
$app = Flight::app();

// Apply configuration
foreach ($config as $key => $value) {
    if ($key === 'database') {
        // Handle database configuration separately
        continue;
    }
    $app->set($key, $value);
}

// Configure database connection
if (isset($config['database'])) {
    $dbConfig = $config['database'];

    try {
        if (isset($dbConfig['file_path'])) {
            // SQLite configuration
            $dsn = 'sqlite:' . $dbConfig['file_path'];
            $pdo = new PDO($dsn);
        } else {
            // MySQL configuration
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $dbConfig['host'] ?? 'localhost',
                $dbConfig['dbname'] ?? 'flightphp',
                $dbConfig['charset'] ?? 'utf8mb4'
            );
            $pdo = new PDO(
                $dsn,
                $dbConfig['user'] ?? null,
                $dbConfig['password'] ?? null,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }

        // Store PDO instance in Flight
        $app->set('pdo', $pdo);

        // Register database helper function
        Flight::map('db', function() use ($pdo) {
            return $pdo;
        });

    } catch (Exception $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        $app->set('pdo', null);
    }
}

// Load controllers
require_once __DIR__ . '/../controllers/auth.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PageController.php';
require_once __DIR__ . '/../controllers/MessagesController.php';

// Load routes
require_once __DIR__ . '/routes.php';

// Start FlightPHP
$app->start();