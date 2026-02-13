<?php
session_start();

$_SESSION = []; // Unset all session variables

session_destroy();

if (ini_get("session.use_cookies")) {   // Delete the session cookie
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

header("Location: ../login.html");
exit;
?>