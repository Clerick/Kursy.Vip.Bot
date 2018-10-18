<?php
require_once '../vendor/autoload.php';

use App\Models\FilebaseDB;

$db = new FilebaseDB();

$item = $db->userHasInstanceId(124);
echo '<pre>';
var_dump($item);