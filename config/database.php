<?php

define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT') ?: '3306';
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASSWORD');

    if (!$dbHost || !$dbName || !$dbUser) {
        error_log('Variables MySQL manquantes sur Railway.');

        http_response_code(500);

        die(json_encode([
            'success' => false,
            'message' => 'Configuration de la base de données manquante.'
        ]));
    }

    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=" . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    } catch (PDOException $e) {
        error_log('Erreur DB : ' . $e->getMessage());

        http_response_code(500);

        die(json_encode([
            'success' => false,
            'message' => 'Erreur de connexion à la base de données.'
        ]));
    }

    return $pdo;
}
