<?php

define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {

        
        $dbHost = getenv('MYSQLHOST') ?: 'localhost';
        $dbPort = getenv('MYSQLPORT') ?: '3306';
        $dbName = getenv('MYSQLDATABASE') ?: 'femiempire';
        $dbUser = getenv('MYSQLUSER') ?: 'root';
        $dbPass = getenv('MYSQLPASSWORD') ?: '';

        if (!$dbHost || !$dbName || !$dbUser || !$dbPass) {
            error_log('Variables MySQL Railway manquantes');
            error_log('MYSQLHOST=' . ($dbHost ?: 'NOT_SET'));
            error_log('MYSQLPORT=' . ($dbPort ?: 'NOT_SET'));
            error_log('MYSQLDATABASE=' . ($dbName ?: 'NOT_SET'));
            error_log('MYSQLUSER=' . ($dbUser ?: 'NOT_SET'));
            error_log('MYSQLPASSWORD=' . ($dbPass ? 'SET' : 'NOT_SET'));

            throw new RuntimeException('Configuration MySQL manquante');
        }
        
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
