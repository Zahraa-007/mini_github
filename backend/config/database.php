<?php

$host = "localhost";
$dbname = "mini_github";
$username = "root";
$password = "zozo2007!";

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);
    die("Database connection failed");
}
