<?php
require_once __DIR__ . "/../core/init.php";

if (
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["role"] ?? "") !== "admin"
) {
    response(403, "Admin access required");
}

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response(405, "Method not allowed");
}

$stmt = $conn->query(
    "SELECT _username, _email, _role
     FROM users
     ORDER BY _username ASC"
);

$users = $stmt->fetchAll();

$response = [
    "users" => [],
    "admins" => []
];

foreach ($users as $user) {
    $account = [
        "username" => $user["_username"],
        "email" => $user["_email"]
    ];

    if ($user["_role"] === "admin") {
        $response["admins"][] = $account;
    } else {
        $response["users"][] = $account;
    }
}

response(200, "Accounts loaded successfully", $response);
