
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

    $tables = ["users", "admins"];

    foreach ($tables as $table) {
        $check = $conn->prepare(
            "SELECT COUNT(*) FROM $table
             WHERE _username = :username OR _email = :email"
        );

        $check->execute([
            ":username" => $username,
            ":email" => $email
        ]);

        if ($check->fetchColumn() > 0) {
            response(409, "Username or email already exists");
        }
    }

    $table = $isAdmin ? "admins" : "users";
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO $table (_username, _email, _password)
         VALUES (:username, :email, :password)"
    );

    $stmt->execute([
        ":username" => $username,
        ":email" => $email,
        ":password" => $hashedPassword
    ]);

    response(201, "Account added successfully");
}

if ($action === "delete") {
    $username = trim($data["username"] ?? "");
    $isAdmin = !empty($data["is_admin"]);

    if ($username === "") {
        response(400, "Username is required");
    }

    if ($isAdmin) {
        if ($username === $_SESSION["user"]["username"]) {
            response(400, "You cannot delete your own account");
        }

        $count = $conn->query(
            "SELECT COUNT(*) FROM admins"
        )->fetchColumn();

        if ($count <= 1) {
            response(400, "Cannot delete the last admin");
        }
    }

    $table = $isAdmin ? "admins" : "users";

    $stmt = $conn->prepare(
        "DELETE FROM $table WHERE _username = :username"
    );

    $stmt->execute([
        ":username" => $username
    ]);

    if ($stmt->rowCount() === 0) {
        response(404, "Account not found");
    }

    response(200, "Account deleted successfully");
}

response(400, "Invalid action");
