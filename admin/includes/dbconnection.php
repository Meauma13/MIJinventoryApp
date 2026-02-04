<?php
$host = getenv('DB_HOST') ?: "Localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$db = getenv('DB_NAME') ?: "inventorydb";

$con=mysqli_connect($host, $user, $pass, $db);
// echo "Connection Fail".mysqli_connect_error();
  ?>
