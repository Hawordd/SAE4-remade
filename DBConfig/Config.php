<?php

namespace DBConfig;

class Config
{
    private static array $settings = [];
    private static bool $initialized = false;

    private static function initialize(): void
    {
        if (!self::$initialized) {
            // Read .env file manually
            $envFile = dirname(__DIR__) . '/.env';
            
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    // Skip comments
                    if (strpos(trim($line), '//') === 0) {
                        continue;
                    }
                    
                    $parts = explode('=', $line, 2);
                    if (count($parts) === 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);
                        putenv("$key=$value");
                        $_ENV[$key] = $value;
                    }
                }
            }

            self::$settings = [
                'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost',
                'dbname' => $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? '',
                'user' => $_ENV['DB_USER'] ?? getenv('DB_USER') ?? '',
                'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '',
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