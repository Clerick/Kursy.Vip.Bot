<?php
require_once '../vendor/autoload.php';

$dot_path = dirname(__DIR__);

$dotenv = new Dotenv\Dotenv($dot_path);
$dotenv->load();

require_once '../app/Controllers/BotController.php';
