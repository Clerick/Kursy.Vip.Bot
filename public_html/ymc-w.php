<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../vendor/autoload.php';
$root_path = dirname(__FILE__, 2);

$dotenv = new Dotenv\Dotenv($root_path);
$dotenv->load();

use App\Controllers\YMController;

$ymc = new YMController();
$auth_url = $ymc->getAuthUrl();



echo '<pre>';
var_dump($auth_url);