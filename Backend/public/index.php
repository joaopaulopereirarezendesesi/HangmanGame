<?php
require_once 'config/config.php';
require_once 'core/Database.php';
require_once 'core/Router.php';

$router = new Router();
$router->run();
