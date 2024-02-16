<?php

/**
 * User Session and EswLogin Management
 *
 * This PHP script handles user session configuration, defines constants,
 * sets secure cookie parameters, and provides logic for user EswLogin.
 *
 * PHP version 7.0 or higher
 *
 * @category EswLogin
 * @package  User Session and EswLogin Management
 * @author   José Caruajulca
 */

// Set the session name
session_name('esw_session');

// Define constants
define('HOME_URL', 'home');
define('CSRF_TOKEN', 'csrf_token');

// Configure session cookie duration (365 days)
$cookieLifetime = 365 * 24 * 60 * 60;

// Get current session cookie parameters
$cookieParams = session_get_cookie_params();

// Configure new session cookie parameters
session_set_cookie_params(
    $cookieLifetime,         // Cookie duration in seconds
    $cookieParams["path"],   // Cookie path (same as current)
    $cookieParams["domain"], // Cookie domain (same as current)
    true,                    // Use only secure cookies (SSL)
    true                     // Allow only JavaScript access (HttpOnly)
);

// Start the session
session_start();

// Include database configuration file
require_once('app/config/db.php');

/**
 * EswLogin Class
 *
 * Provides methods for user EswLogin.
 */
class EswLogin
{
    /**
     * Authenticate user
     *
     * @param string $usernameOrEmail Username or email address
     * @param string $password       Password
     *
     * @return void
     */
    public static function loginUser($usernameOrEmail, $password)
    {
        try {
            $connection = Database::connect();

            if (!$connection) {
                throw new Exception("Database connection error.");
            }

            $query = "SELECT * FROM users WHERE username = :username OR email = :email";
            $statement = $connection->prepare($query);
            $statement->bindParam(':username', $usernameOrEmail, PDO::PARAM_STR);
            $statement->bindParam(':email', $usernameOrEmail, PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                $user = $statement->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    echo json_encode(['success' => true, 'success_message' => 'Successful login', 'redirect' => HOME_URL]);
                    exit();
                } else {
                    throw new Exception("Invalid credentials.");
                }
            } else {
                throw new Exception("Invalid credentials.");
            }
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage(), 0);
            echo json_encode(['success' => false, 'error_message' => 'Error attempting to log in, please try again later.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error_message' => $e->getMessage()]);
        } finally {
            if ($connection) {
                $connection = null;
            }
        }
    }
}

// Check if the request is of type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Filter and get form data
        $usernameOrEmail = filter_input(INPUT_POST, 'username_or_email', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        // Check if required fields are complete
        if (empty($usernameOrEmail) || empty($password)) {
            throw new Exception("Please fill in all fields.");
        }

        // Check CSRF token validity
        if (!isset($_POST[CSRF_TOKEN]) || !hash_equals($_POST[CSRF_TOKEN], $_SESSION[CSRF_TOKEN])) {
            throw new Exception("Invalid CSRF token.");
        }

        // Call the method to authenticate the user
        EswLogin::loginUser($usernameOrEmail, $password);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error_message' => $e->getMessage()]);
    }
}
