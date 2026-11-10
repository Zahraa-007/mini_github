<?php
require_once __DIR__ . "/../core/init.php";

requirePost();
$data = getInput();

$usernameOrEmail = trim($data["username"] ?? "");
$password = $data["password"] ?? "";

if ($usernameOrEmail === "" || $password === "") {
    response(400, "Username and password are required");
}

$stmt = $conn->prepare(
    "SELECT _username, _email, _password, _role
     FROM users
     WHERE _username = :username OR _email = :email
     LIMIT 1"
);

$stmt->execute([
    ":username" => $usernameOrEmail,
    ":email" => $usernameOrEmail
]);

$user = $stmt->fetch();

if (!$user || !password_verify($password, $user["_password"])) {
    response(401, "Invalid username or password");
}

if ($user["_role"] !== "admin") {
    response(403, "Access denied. Admins only");
}

session_regenerate_id(true);

$_SESSION["user"] = [
    "username" => $user["_username"],
    "email" => $user["_email"],
    "role" => "admin"
];

response(200, "Admin login successful");
?>