<?php
error_reporting(0);

require_once '../vendor/autoload.php';

use App\Models\Bot;

$root_path = dirname(__FILE__, 2);

$dotenv = new Dotenv\Dotenv($root_path);
$dotenv->load();
$dotenv->required(['BOT_TOKEN', 'ADMIN']);

$bot = new Bot();
$bot->start();
