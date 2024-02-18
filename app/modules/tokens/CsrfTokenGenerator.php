<?php
// Check if there is no CSRF token in the session
if (!isset($_SESSION['csrf_token'])) {
    // Generate a new CSRF token using random bytes
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}