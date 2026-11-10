<?php
require_once __DIR__ . "/../core/init.php";

requirePost();

if (
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["role"] ?? "") !== "admin"
) {
    response(403, "Admin access required");
}

$data = getInput();
$action = $data["action"] ?? "";

if ($action === "add") {

    $username = trim($data["username"] ?? "");
    $email = trim($data["email"] ?? "");
    $password = $data["password"] ?? "";
    $isAdmin = !empty($data["is_admin"]);

    if ($username === "" || $email === "" || $password === "") {
        response(400, "All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response(400, "Invalid email address");
    }

    if (strlen($password) < 8) {
        response(400, "Password must be at least 8 characters");
    }

    $check = $conn->prepare(
        "SELECT COUNT(*) FROM users
         WHERE _username = :username OR _email = :email"
    );

    $check->execute([
        ":username" => $username,
        ":email" => $email
    ]);

    if ($check->fetchColumn() > 0) {
        response(409, "Username or email already exists");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = $isAdmin ? "admin" : "user";

    $stmt = $conn->prepare(
        "INSERT INTO users (_username, _email, _password, _role)
         VALUES (:username, :email, :password, :role)"
    );

    $stmt->execute([
        ":username" => $username,
        ":email" => $email,
        ":password" => $hashedPassword,
        ":role" => $role
    ]);

    response(201, "Account added successfully");
}

if ($action === "delete") {

    $username = trim($data["username"] ?? "");
    $role = ($data["is_admin"] ?? false) ? "admin" : "user";

    if ($username === "") {
        response(400, "Username is required");
    }

    if (
        $role === "admin" &&
        $username === $_SESSION["user"]["username"]
    ) {
        response(400, "You cannot delete your own account");
    }

    if ($role === "admin") {
        $count = $conn->query(
            "SELECT COUNT(*) FROM users WHERE _role = 'admin'"
        )->fetchColumn();

        if ($count <= 1) {
            response(400, "Cannot delete the last admin");
        }
    }

    $stmt = $conn->prepare(
        "DELETE FROM users
         WHERE _username = :username AND _role = :role"
    );

    $stmt->execute([
        ":username" => $username,
        ":role" => $role
    ]);

    if ($stmt->rowCount() === 0) {
        response(404, "Account not found");
    }

    response(200, "Account deleted successfully");
}

response(400, "Invalid action");
