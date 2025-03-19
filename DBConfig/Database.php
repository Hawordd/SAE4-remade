<?php

namespace DBConfig;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $db = null;

    public static function getConnection(): PDO
    {
        if (self::$db === null) {
            try {
                self::$db = new PDO(
                    'mysql:host=' . Config::get('host') . ';dbname=' . Config::get('dbname'),
                    Config::get('user'),
                    Config::get('password')
                );
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }
        return self::$db;
    }
}