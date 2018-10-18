<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../vendor/autoload.php';

use \YandexMoney\API;
use App\Controllers\YMController;

$root_path = dirname(__FILE__, 2);

$dotenv = new Dotenv\Dotenv($root_path);
$dotenv->load();
$dotenv->required(['BOT_TOKEN', 'ADMIN']);

$client_id = getenv("YANDEX_CLIENT_ID");
$redirect_uri = getenv("YANDEX_REDIRECT_URI");

// if(isset($_GET['code'])) {
    // $code = $_GET['code'];
    // $code = filter_var($_GET['code'], FILTER_SANITIZE_STRING);
    $ymController = new YMController();
    // $ymController->setAccessToken($code);
    // $accInfo = $ymController->getAccountInfo();
    // file_put_contents('newfile.txt', $accInfo);
    echo '<pre>';
    // var_dump($accInfo);

    $ymController->makePaymentFromWallet();


    echo '</pre>';
// } else {
//     $ymController = new YMController();
//     $auth_url = $ymController->getAuthUrl();
//     header('Location: ' . $auth_url);
//     die();
// }


// $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $accInfo);
// fclose($myfile);

