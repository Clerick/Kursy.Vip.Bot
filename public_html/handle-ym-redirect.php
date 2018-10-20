<?php

error_reporting(0);

require_once '../vendor/autoload.php';

use App\Controllers\YMController;
use App\Models\Bot;
use App\Factories\CourseFactory;

$root_path = dirname(__FILE__, 2);

$dotenv = new Dotenv\Dotenv($root_path);
$dotenv->load();
$dotenv->required(['BOT_TOKEN', 'ADMIN']);

$client_id = getenv("YANDEX_CLIENT_ID");
$redirect_uri = getenv("YANDEX_REDIRECT_URI");

$paramsString = filter_input(INPUT_GET, 'params', FILTER_SANITIZE_STRING);
$code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
list($chatId, $courseName) = explode("-", $paramsString);

$bot = new Bot();

$course = CourseFactory::build($courseName);
if ($course == null || $chatId == null) {
    $message = "Вы перешли по неверному адресу.\n" .
        "Пожалуйста, свяжитесь с нашей техподдержкой\n" .
        "https://t.me/" . getenv("ADMIN");
    die();
}

$ymController = new YMController($chatId, $course);

if ($code != null) {
    $ymController->setAccessToken($code);
    $result = $ymController->makePaymentFromWallet();

    if ($result == "payment_success") {
        $bot->sendCourseLink($chatId, $course->getCourseUrl());
        backToBot();
    }

    if ($result == "not_enough_funds") {
        $message = "У вас недостаточно средств для покупки";
        $bot->sendMessage($chatId, $message);
        backToBot();
    }

    $message = "Возникла ошибка при проведении платежа." .
        "Пожалуйста, свяжитесь с нашим администратором\n" .
        "https://t.me/" . getenv('ADMIN') . "\nИ укажите код ошибки:" .
        $result;
    $bot->sendMessage($chatId, $message);
    backToBot();
} else {
    $ymController = new YMController($chatId, $course);
    $auth_url = $ymController->getAuthUrl();
    header('Location: ' . $auth_url);
    die();
}

function backToBot()
{
    header("Location: https://t.me/kursy_vip_bot");
    die();
}
