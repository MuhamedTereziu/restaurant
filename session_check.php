<?php
// Secure session handling
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: index.html");
    exit();
}
?>
