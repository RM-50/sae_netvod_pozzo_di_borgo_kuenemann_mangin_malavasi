<?php


declare(strict_types=1);

use iutnc\netvod\dispatch\Dispatcher;

require_once 'vendor/autoload.php';

session_start();

$action = isset($_GET['action']) ? $_GET['action'] : null;
$dispatcher = new Dispatcher($action);
$dispatcher->run();
