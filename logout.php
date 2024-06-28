<?php
session_start();

// Empty all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the Home page
header('Location: index.php');
exit;
?>
