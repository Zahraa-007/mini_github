<?php

require_once __DIR__ . "/../core/init.php";

requirePost();

$data = getInput();

$name = trim($data["_username"] ?? "");
$email = strtolower(trim($data["_email"] ?? ""));
$password = $data["_password"] ?? "";

if ($name === "" || $email === "" || $password === "") {
    response(400, "All fields are required");
}

if (strlen($name) > 100) {
    response(400, "Name is too long");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    response(400, "Invalid email address");
}

if (strlen($password) < 8) {
    response(400, "Password must be at least 8 characters");
}

if (strlen($password) > 72) {
    response(400, "Password is too long");
}

$stmt = $conn->prepare(
    "SELECT _username FROM users WHERE _email = :email"
);

$stmt->execute([
    ":email" => $email
]);

if ($stmt->fetch()) {
    response(409, "Email already exists");
}

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, password)
     VALUES (:name, :email, :password)"
);

$stmt->execute([
    ":name" => $name,
    ":email" => $email,
    ":password" => $hashedPassword
]);

// $userId = $conn->lastInsertId();

session_regenerate_id(true);

$_SESSION["user"] = [
    // "id" => $userId,
    "name" => $name,
    "email" => $email
];

response(201, "Account created successfully");
