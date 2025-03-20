<?php

namespace DBConfig;

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

class Config
{
    private static array $settings = [];
    private static bool $initialized = false;

    private static function initialize(): void
    {
        if (!self::$initialized) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->safeLoad();

            self::$settings = [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'dbname' => $_ENV['DB_NAME'] ?? '',
                'user' => $_ENV['DB_USER'] ?? '',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
            ];

            self::$initialized = true;
        }
    }

    public static function get($key)
    {
        self::initialize();
        return self::$settings[$key] ?? null;
    }
}