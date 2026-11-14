
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

$usersStmt = $conn->query(
    "SELECT _username AS username, _email AS email
     FROM users
     ORDER BY _username ASC"
);

$adminsStmt = $conn->query(
    "SELECT _username AS username, _email AS email
     FROM admins
     ORDER BY _username ASC"
);

response(200, "Accounts loaded successfully", [
    "users" => $usersStmt->fetchAll(PDO::FETCH_ASSOC),
    "admins" => $adminsStmt->fetchAll(PDO::FETCH_ASSOC)
]);
