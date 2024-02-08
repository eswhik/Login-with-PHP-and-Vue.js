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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: aliceblue;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        #app {
            max-width: 400px;
            width: 100%;
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007BFF;
            color: white;
            text-align: center;
            border-bottom: none;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007BFF;
            border: none;
        }
    </style>
</head>

<body>
    <div id="app">
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
    <script>
        /**
         * Toast Configuration and Vue App Setup for Login Form
         *
         * This script configures Toast (a Swal component) and creates a Vue App instance
         * to manage the login form.
         *
         * @category Frontend
         * @package Toast and Vue App Configuration
         * @author José Caruajulca
         */

        // Configure Toast for notifications
        const Toast = Swal.mixin({
            toast: true, // Toast notifications
            position: "top-end", // Position at the top right
            showConfirmButton: false, // Do not show confirmation button
            timer: 3000, // Default duration of 3 seconds
            timerProgressBar: true, // Progress bar during the duration
            didOpen: (toast) => {
                // Pause timer when hovering over the notification
                toast.onmouseenter = Swal.stopTimer;
                // Resume timer when mouse leaves the notification
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Create Vue App instance
        const app = Vue.createApp({
            data() {
                return {
                    csrfToken: '<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>',
                    formData: {
                        usernameOrEmail: '',
                        password: ''
                    }
                };
            },
            methods: {
                /**
                 * Handle login form submission
                 *
                 * This method sends a POST request to the backend with form data
                 * and handles responses, showing Toast notifications or redirecting as needed.
                 */
                formLogin() {
                    const formData = new FormData();
                    formData.append('csrf_token', this.csrfToken);
                    formData.append('username_or_email', this.formData.usernameOrEmail);
                    formData.append('password', this.formData.password);

                    fetch('backend', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success notification
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success_message,
                                    showConfirmButton: true
                                }).then((result) => {
                                    // Redirect if confirmed and there is a redirect URL
                                    if (result.isConfirmed && data.redirect) {
                                        window.location.href = data.redirect;
                                    }
                                });
                            } else {
                                // Show error notification
                                Toast.fire({
                                    icon: "error",
                                    title: data.error_message
                                });
                            }
                        })
                        .catch(error => {
                            // Show error notification in case of request failure
                            Toast.fire({
                                icon: "error",
                                title: error
                            });
                        });
                }
            }
        });

        // Mount the Vue App on the element with the id 'app' in the DOM
        app.mount('#app');
    </script>
</body>

</html>