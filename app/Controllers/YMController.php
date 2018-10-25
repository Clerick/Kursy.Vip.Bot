<?php

namespace App\Controllers;

use \YandexMoney\API;
use \YandexMoney\ExternalPayment;
use App\Models\BaseCourse;
use App\Models\FilebaseDB;

class YMController
{

    private $chatId;

    /**
     * @var BaseCourse
     */
    private $course;

    /**
     * @var array
     */
    private $scope;

    /**
     * @var string
     */
    private $client_id;

    /**
     * @var string
     */
    private $redirect_uri;

    /**
     * @var API
     */
    private $api;

    /**
     * @var FilebaseDB
     */
    private $db;

    public function __construct(int $chatId, BaseCourse $course)
    {
        $this->client_id = getenv("YANDEX_CLIENT_ID");
        $this->redirect_uri = getenv("YANDEX_REDIRECT_URI");
        $this->db = new FilebaseDB();

        $this->chatId = $chatId;
        $this->course = $course;

        $this->scope = [
            "payment.to-account(\"" . getenv("YANDEX_WALLET") .
            "\").limit(," . $this->course->getPrice() . ")",
        ];
    }

    /**
     *
     * @return string
     */
    public function getAuthUrl(): string
    {
        $redirect_uri = $this->redirect_uri .
            "?params=" . $this->chatId . "-" . $this->course->getShortName();
        $auth_url = API::buildObtainTokenUrl($this->client_id, $redirect_uri, $this->scope);
        return $auth_url;
    }

    /**
     *
     * @return string
     */
    public function getYMWalletUrl(): string
    {
        $url = $this->redirect_uri .
            "?params=" . $this->chatId . "-" . $this->course->getShortName();
        return $url;
    }

    /**
     *
     * @param type string $code
     * @throws \Exception
     */
    public function setAccessToken(string $code)
    {
        $access_token_response = API::getAccessToken($this->client_id, $code, $this->redirect_uri);

        if (property_exists($access_token_response, "error")) {
            throw new \Exception("setAccessToken fail. " . $access_token_response->error);
        }

        $access_token = $access_token_response->access_token;
        $this->db->setChatAccessToken($this->chatId, $access_token);

        $this->api = new API($access_token);
    }

    public function requestPayment()
    {
        if ($this->api == null) {
            throw new \Exception("Access token is not set");
        }

        $requestPayment = $this->api->requestPayment([
            "pattern_id" => "p2p",
            "to" => getenv('YANDEX_WALLET'),
            "amount_due" => $this->course->getPrice(),
            "comment" => $this->course->getDescription(),
            "message" => "Оплата за курс",
//            "test_payment" => "true",
//            "test_result" => "success",
        ]);

        return $requestPayment;
    }

    public function makePaymentFromWallet()
    {
        $requestPayment = $this->requestPayment();
        if ($requestPayment->status == "refused") {
            return($requestPayment->error);
        }

        do {
            $process_payment = $this->api->processPayment([
                "request_id" => $requestPayment->request_id,
//                "test_payment" => "true",
//                "test_result" => "success",
            ]);

            switch ($process_payment->status) {
                case "in_progress":
                    sleep(2);
                    break;
                case "success":
                    $this->db->savePaymentId($this->chatId, $process_payment->payment_id);
                    return "payment_success";
                case "refused":
                    return $process_payment->error;

                default:
                    return $process_payment->error;
            }
        } while ($process_payment->status == "in_progress");
    }

    public function getInstanceId()
    {
        if ($this->db->userHasInstanceId($this->chatId)) {
            return $this->db->getInstanceId($this->chatId);
        } else {
            $response = ExternalPayment::getInstanceId($this->client_id);
            if ($response->status == "success") {
                $instance_id = $response->instance_id;
                $this->db->setUserInstanceId($this->chatId, $instance_id);
                return $instance_id;
            } else {
                throw new \Exception($response->error);
            }
        }
    }

    public function makePayment()
    {
        $instance_id = $this->getInstanceId();
        $external_payment = new ExternalPayment($instance_id);

        $payment_options = [
            "pattern_id" => "p2p",
            "to" => getenv('YANDEX_WALLET'),
            "amount" => $this->course->getPrice(),
            "comment" => "sample test payment comment",
            "message" => "Оплата курса course_name",
            "test_payment" => "true",
            "test_result" => "success"
        ];

        $response = $external_payment->request($payment_options);

        if ($response->status == "success") {
            $request_id = $response->request_id;

            $process_options = ["request_id" => $request_id,
                "instance_id" => $this->getInstanceId(),
                "ext_auth_success_uri" => "http://mih1984.beget.tech/ymc-success.php",
                "ext_auth_fail_uri" => "http://mih1984.beget.tech/ymc-fail.php",
                "test_payment" => "true",
                "test_result" => "success",
            ];
            do {
                $result = $external_payment->process($process_options);
                if ($result->status == "in_progress") {
                    sleep(5);
                }
                if ($result->status == "ext_auth_required") {
                    $url = sprintf(
                        "%s?%s",
                        $result->acs_uri,
                        http_build_query($result->acs_params)
                    );

                    var_dump($result->acs_params);
//					header('Location: ' . $url);
                    die();
                }
                if ($result->status == "success") {
                    $this->db->saveInvoiceId($this->chatId, $result->invoice_id);
                    return "payment_success";
                }
            } while ($result->status == 'in_progress');
        } else {
            var_dump($response->error);
            // throw exception with $response->message
            throw new \Exception($response->message);
        }
    }

}
