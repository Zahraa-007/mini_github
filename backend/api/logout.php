<?php

require_once __DIR__ . "/../core/init.php";

requirePost();

$_SESSION = [];

if (ini_get("session.use_cookies")) {

    setcookie(session_name(), "", [
        "expires" => time() - 42000,
        "path" => "/",
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}

session_destroy();

response(200, "Logout successful");
