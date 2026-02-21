<?php
session_start();
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Se destruye la sesión en el servidor
session_destroy();

// 4. Se redirige al usuario a index.php
header("Location: ../index.php");
exit;
?>