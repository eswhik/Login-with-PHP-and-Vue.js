<?php

/**
 * Database Class
 *
 * This class provides a connection to the database using the PDO extension in PHP.
 * Connection details are defined as private static properties.
 * Options are included to enhance security and error handling.
 */
class Database
{
    /** @var string Database name */
    private static $db_name = "plantilla";

    /** @var string Database username */
    private static $db_user = "root";

    /** @var string Database password */
    private static $password = "";

    /** @var string Database server */
    private static $db_server = "localhost";

    /** @var array Configuration options for the PDO connection */
    private static $options = [
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    /**
     * Method connect
     *
     * Establishes a connection to the database using the defined connection details.
     *
     * @return PDO|null PDO object if the connection is successful, or null if there is an error.
     */
    public static function connect()
    {
        try {
            $connection = new PDO("mysql:host=" . self::$db_server . ";dbname=" . self::$db_name, self::$db_user, self::$password, self::$options);

            // echo "Successful database connection";
            return $connection;
        } catch (PDOException $e) {
            error_log("Connection error: " . $e->getMessage(), 0);
            echo "Connection error. Please try again later.";
            return null;
        }
    }
}

// Example of usage
$connection = Database::connect();
