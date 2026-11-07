<?php

ini_set("session.use_strict_mode", "1");

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => false,
    "httponly" => true,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/database.php";

function response($status, $message, $data = null)
{

    http_response_code($status);

    echo json_encode([
        "success" => $status >= 200 && $status < 300,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

function getInput()
{

    $input = json_decode(file_get_contents("php://input"), true);

    return is_array($input) ? $input : [];
}

function requirePost()
{

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        response(405, "Method not allowed");
    }
}
?>