<?php
require_once '../vendor/autoload.php';

use App\Models\Bot;

$dot_path = dirname(__DIR__);

$dotenv = new Dotenv\Dotenv($dot_path);
$dotenv->load();

$bot = new Bot();
$bot->start();
