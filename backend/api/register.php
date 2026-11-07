<?php
require_once __DIR__ . "/../core/init.php";

requirePost();
$data = getInput();

$username = trim($data["username"] ?? "");
$email = strtolower(trim($data["email"] ?? ""));
$password = $data["password"] ?? "";
$confirmPassword = $data["confirmPassword"] ?? "";
$terms = $data["terms"] ?? false;

if ($username === "" || $email === "" || $password === "") {
    response(400, "All fields are required");
}

if (!preg_match('/^[A-Za-z0-9_-]{3,20}$/', $username)) {
    response(400, "Invalid username");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    response(400, "Invalid email address");
}

if (strlen($password) < 8 || strlen($password) > 72) {
    response(400, "Password must be between 8 and 72 bytes");
}

if ($password !== $confirmPassword) {
    response(400, "Passwords do not match");
}

if ($terms !== true) {
    response(400, "You must accept the terms");
}


  $stmt = $conn->prepare(
    "SELECT _username FROM users WHERE _username = :username OR _email = :email"
);


$stmt->execute([
    ":username" => $username,
    ":email" => $email
]);

if ($stmt->fetch()) {
    response(409, "Username or email already exists");
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (_username, _email, _password)
     VALUES (:username, :email, :password)"
);

$stmt->execute([
    ":username" => $username,
    ":email" => $email,
    ":password" => $hashedPassword
]);


session_regenerate_id(true);

$_SESSION["user"] = [
    "username" => $username,
    "email" => $email
];

response(201, "Account created successfully");
