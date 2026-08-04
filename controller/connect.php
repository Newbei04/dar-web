<?php

$ENV = parse_ini_file(__DIR__ . '/../.env');

$conn = new mysqli(
    $ENV['DB_HOST'],
    $ENV['DB_USER'],
    $ENV['DB_PASS'],
    $ENV['DB_NAME']
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
