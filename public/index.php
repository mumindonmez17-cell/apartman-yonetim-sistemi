<?php

session_start();

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/core/App.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/Database.php';
$config = require_once __DIR__ . '/../config/config.php';

// Professional Error Handling
if ($config['app_debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

$app = new App();
