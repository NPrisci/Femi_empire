<?php

define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {

        /*
         * Railway utilise actuellement les variables DB_* :
         *
         * DB_HOST     = ${{MySQL.MYSQLHOST}}
         * DB_PORT     = ${{MySQL.MYSQLPORT}}
         * DB_NAME     = ${{MySQL.MYSQLDATABASE}}
         * DB_USER     = ${{MySQL.MYSQLUSER}}
         * DB_PASSWORD = ${{MySQL.MYSQLPASSWORD}}
         *
         * Les MYSQL* sont conservées en fallback.
         */

        $dbHost = getenv('DB_HOST') ?: getenv('MYSQLHOST');
        $dbPort = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';
        $dbName = getenv('DB_NAME') ?: getenv('MYSQLDATABASE');
        $dbUser = getenv('DB_USER') ?: getenv('MYSQLUSER');
        $dbPass = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD');

        /*
         * Vérification de la configuration
         */
        if (!$dbHost || !$dbName || !$dbUser || !$dbPass) {

            error_log('=== CONFIGURATION MYSQL MANQUANTE ===');
            error_log('DB_HOST=' . ($dbHost ?: 'NOT_SET'));
            error_log('DB_PORT=' . ($dbPort ?: 'NOT_SET'));
            error_log('DB_NAME=' . ($dbName ?: 'NOT_SET'));
            error_log('DB_USER=' . ($dbUser ?: 'NOT_SET'));
            error_log('DB_PASSWORD=' . ($dbPass ? 'SET' : 'NOT_SET'));

            throw new RuntimeException(
                'Configuration MySQL manquante'
            );
        }

        /*
         * Construction de la connexion PDO
         */
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $dbHost,
            $dbPort,
            $dbName,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {

            $pdo = new PDO(
                $dsn,
                $dbUser,
                $dbPass,
                $options
            );

            /*
             * Log utile pour vérifier Railway sans exposer
             * le mot de passe.
             */
            error_log(
                sprintf(
                    'Connexion MySQL réussie : %s:%s/%s',
                    $dbHost,
                    $dbPort,
                    $dbName
                )
            );

        } catch (PDOException $e) {

            /*
             * Le mot de passe n'est jamais écrit dans les logs.
             */
            error_log(
                'Erreur DB : ' . $e->getMessage()
            );

            http_response_code(500);

            header('Content-Type: application/json; charset=utf-8');

            die(json_encode(
                [
                    'success' => false,
                    'message' => 'Erreur de connexion à la base de données.'
                ],
                JSON_UNESCAPED_UNICODE
            ));
        }
    }

    return $pdo;
}
