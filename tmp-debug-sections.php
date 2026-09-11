<?php
require 'sections.php';
$ENV = parse_ini_file(__DIR__ . '/.env');
$baseURL = base_url() . '/';
$basePath = base_url() . '/index.php';
ob_start();
require 'views/users-list.php';
ob_end_clean();
global $sections;
foreach ($sections as $k => $v) {
    echo $k . '=[' . strlen($v) . '] ' . var_export(substr($v, 0, 120), true) . PHP_EOL;
}
