<?php
header("Access-Control-Allow-Origin: *");  
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");  
header("Access-Control-Allow-Headers: Content-Type"); 

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

require_once 'config\config.php';
require_once 'core\Database.php';
require_once 'core\Router.php';

$router = new Router();
$router->run();
