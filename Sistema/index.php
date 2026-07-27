<?php
include 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
$conn->select_db(DB_NAME);

$conn->query("CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
)");

$conn->close();

header('location:login.php');
exit();
?>