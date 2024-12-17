<?php
class Database
{
    private static $instance = null;

    public static function getConnection()
    {
        if (self::$instance === null) {
            try {
                $dotenv = parse_ini_file(__DIR__ . '/../.env');

                $host = $dotenv['DB_HOST'];
                $dbname = $dotenv['DB_NAME'];
                $user = $dotenv['DB_USER'];
                $pass = $dotenv['DB_PASS'];

                self::$instance = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                exit("Database Connection Failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
