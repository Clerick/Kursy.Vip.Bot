<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

use App\Models\Bot;
use unreal4u\TelegramAPI\Telegram\Types\Update;

$updateData = json_decode(file_get_contents('php://input'), true);

$update = new Update($updateData);

$bot = new Bot();

if ($update->message != null) {
    $this->handleMessageUpdate($update);
}

if ($update->callback_query != null) {
    $this->handleCallbackQueryUpdate($update->callback_query);
}
