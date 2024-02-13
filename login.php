<?php

/**
 * Session Configuration and CSRF Token Generation
 *
 * This PHP script handles user session configuration, ensuring
 * secure cookie parameters, and generating a CSRF token for protection against CSRF attacks.
 *
 * PHP version 7.0 or higher
 *
 * @category Configuration
 * @package  Sessions and CSRF Security
 * @author   José Caruajulca
 */

// Set the session name
session_name('esw_session');

// Define the session cookie duration (365 days)
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

// Check if there is no CSRF token in the session
if (!isset($_SESSION['csrf_token'])) {
    // Generate a new CSRF token using random bytes
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
    <div id="app" data-csrf-token="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Log in</h2>
            </div>
            <div class="card-body">
                <form @submit.prevent="formLogin">
                    <input type="hidden" v-model="csrfToken" name="csrf_token">

                    <div class="mb-3">
                        <label for="username_or_email" class="form-label">Username or email</label>
                        <input type="text" class="form-control" v-model="formData.usernameOrEmail" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" v-model="formData.password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Log in</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.6/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="app/vue/auth-login.js"></script>
</body>

</html>
