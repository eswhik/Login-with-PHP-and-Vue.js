<?php
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