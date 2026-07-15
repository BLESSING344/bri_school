<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $driver   = getenv('DB_CONNECTION') ?: 'pgsql';
            $host     = getenv('DB_HOST') ?: 'db';
            $port     = getenv('DB_PORT') ?: '5432';
            $database = getenv('DB_DATABASE') ?: 'bri_school';
            $username = getenv('DB_USERNAME') ?: 'postgres';
            $password = getenv('DB_PASSWORD') ?: 'postgres';

            $dsn = "{$driver}:host={$host};port={$port};dbname={$database}";

            try {
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }

        return self::$connection;
    }
}
