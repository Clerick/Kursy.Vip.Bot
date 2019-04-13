<?php

require_once '../vendor/autoload.php';


use \React\EventLoop\Factory;
use \unreal4u\TelegramAPI\HttpClientRequestHandler;
use \unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use \unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
use \unreal4u\TelegramAPI\TgLog;
use \unreal4u\TelegramAPI\Telegram\Methods\SetWebhook;

$loop = Factory::create();

$setWebhook = new SetWebhook();
$setWebhook->url = 'https://kursy-vip-bot.herokuapp.com';

$tgLog = new TgLog('649841516:AAHDDVvzhL4antI5aH8Cm9VXh4dOrrjO4N8', new HttpClientRequestHandler($loop));
$tgLog->performApiRequest($setWebhook);
$loop->run();

