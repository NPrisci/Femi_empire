<?php

define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {

        // Railway fournit MYSQLHOST, MYSQLPORT, etc.
        // En local, on utilise les valeurs classiques XAMPP/WAMP.
        $dbHost = getenv('MYSQLHOST') ?: 'localhost';
        $dbPort = getenv('MYSQLPORT') ?: '3306';
        $dbName = getenv('MYSQLDATABASE') ?: 'femiempire';
        $dbUser = getenv('MYSQLUSER') ?: 'root';
        $dbPass = getenv('MYSQLPASSWORD') ?: '';

        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
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
    }

    return $pdo;
}
