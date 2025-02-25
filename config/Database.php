<?php
namespace Config;

use PDO;
use PDOException;


class Database {
    private static $connection = null;

    public static function connect() {
        $servername = "localhost";
        $username = "root";
        $password = "Kamouss@123";
        $dbname = "PlanFlow";

        if (self::$connection === null) {
            try {
                self::$connection = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    public static function close() {
        if (self::$connection !== null) {
            self::$connection = null;
        }
    }
}
?>