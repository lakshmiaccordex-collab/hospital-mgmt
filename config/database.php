<?php
// config/database.php
class Database {
    private static ?PDO $instance = null;
    private static array $env = [];

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::loadEnv();
            $dsn = "mysql:host={$_env['DB_HOST']};port={$_env['DB_PORT']};dbname={$_env['DB_NAME']};charset=utf8mb4";
            $e = self::$env;
            $dsn = "mysql:host={$e['DB_HOST']};port={$e['DB_PORT']};dbname={$e['DB_NAME']};charset=utf8mb4";
            try {
                self::$instance = new PDO($dsn, $e['DB_USER'], $e['DB_PASS'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $ex) {
                die('<div style="font-family:sans-serif;padding:2rem;color:#dc2626;">
                    <h2>⚠️ Database Connection Failed</h2>
                    <p>Please check your <strong>.env</strong> file settings.</p>
                    <code>' . htmlspecialchars($ex->getMessage()) . '</code>
                </div>');
            }
        }
        return self::$instance;
    }

    private static function loadEnv(): void {
        $file = __DIR__ . '/../.env';
        if (!file_exists($file)) {
            self::$env = ['DB_HOST'=>'localhost','DB_PORT'=>'3306','DB_NAME'=>'hospital_mgmt','DB_USER'=>'root','DB_PASS'=>''];
            return;
        }
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
            self::$env[trim($k)] = trim($v);
        }
    }

    public static function env(string $key, string $default = ''): string {
        if (empty(self::$env)) self::loadEnv();
        return self::$env[$key] ?? $default;
    }
}
