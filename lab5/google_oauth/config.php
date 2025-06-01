<?php
// google_oauth/config.php

// Start session if not already started (needed for state parameter)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Path to your composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..'); // Points to lab5 directory for .env
$dotenv->load();

// Google API Credentials from Environment Variables
$googleClientId = $_ENV['GOOGLE_CLIENT_ID'] ?? 'YOUR_GOOGLE_CLIENT_ID'; // Fallback if not set
$googleClientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? 'YOUR_GOOGLE_CLIENT_SECRET'; // Fallback if not set
$googleRedirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost:3000/lab5/google_oauth/google_auth_callback.php'; // Fallback

// Validate that the crucial variables are loaded
if ($googleClientId === 'YOUR_GOOGLE_CLIENT_ID' || $googleClientSecret === 'YOUR_GOOGLE_CLIENT_SECRET') {
    // Log error or die, as the application cannot function without these
    // For a production environment, you'd log this and show a generic error page
    // For development, die() is okay to make the problem obvious.
    error_log('CRITICAL: GOOGLE_CLIENT_ID or GOOGLE_CLIENT_SECRET not loaded from .env file. Please check your .env setup in the lab5 directory.');
    die('Error: Google API credentials are not configured. Check server logs and .env file in the lab5 directory. Ensure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET are set.');
}

// --- Attempt to configure Guzzle HTTP client for SSL verification ---
// IMPORTANT: Replace with the ACTUAL, CORRECT path to your cacert.pem file
$caBundlePath = 'C:/php/ssl/cacert.pem'; // <<< YOU MUST VERIFY THIS PATH IS CORRECT ON YOUR SYSTEM

if (!file_exists($caBundlePath)) {
    error_log("PHP Goggle OAuth: cacert.pem not found at specified path: " . $caBundlePath . ". SSL errors likely to continue.");
    // Optionally, you could die() here if SSL is absolutely critical and you don't want to proceed without it
    // die("Error: CA certificate bundle not found. Please check the path in lab5/google_oauth/config.php");
}

$guzzleClient = new \GuzzleHttp\Client([
    'verify' => $caBundlePath,
    // You might also need to disable system-level CA bundle if it's causing conflicts, but try with verify first.
    // 'verify' => false, // TEMPORARY - FOR DEBUGGING ONLY, VERY INSECURE
]);

// Create Google Client and set the Guzzle client
$google_client = new Google_Client();
$google_client->setHttpClient($guzzleClient); // Set the custom Guzzle client

$google_client->setClientId($googleClientId);
$google_client->setClientSecret($googleClientSecret);
$google_client->setRedirectUri($googleRedirectUri);
$google_client->setAccessType('offline'); // Optional: if you need refresh tokens
$google_client->setPrompt('select_account consent'); // Ensures user always sees consent screen for testing

// Scopes define the permissions you're requesting
$google_client->addScope("email");
$google_client->addScope("profile");
// $google_client->addScope("openid"); // openid is often implicitly included with email/profile

?>