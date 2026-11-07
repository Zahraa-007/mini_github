<?php

require_once __DIR__ . "/../core/init.php";

if (!isset($_SESSION["user"])) {
    response(401, "Unauthorized");
}

response(200, "User authenticated", $_SESSION["user"]);
?>