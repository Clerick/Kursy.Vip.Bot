<?php
namespace App\Controllers;

use \YandexMoney\API;
use \YandexMoney\ExternalPayment;
use App\Models\FilebaseDB;
use \FilebaseDB\Document;

class YMController
{
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

    private $accessToken;

    public function __construct()
    {
        $this->scope = [
            "account-info",
            // "operation-history",
            "payment.to-account(\"" . getenv("YANDEX_WALLET") . "\").limit(7,4000)",
            // "payment.to-account(\"410014197482783\").limit(,500)",
        ];
        $this->client_id = getenv("YANDEX_CLIENT_ID");
        $this->redirect_uri = getenv("YANDEX_REDIRECT_URI");
        $this->db = new FilebaseDB();
    }

    public function getAuthUrl()
    {
        $auth_url = API::buildObtainTokenUrl($this->client_id, $this->redirect_uri, $this->scope);
        return $auth_url;
    }

    public function setAccessToken($code)
    {
        $access_token_response = API::getAccessToken($this->client_id, $code, $this->redirect_uri);

        if (property_exists($access_token_response, "error")) {
            echo "error <br>";
            var_dump($access_token_response);
            // var_dump($access_token_response->error);
        }

        $access_token = $access_token_response->access_token;
        var_dump($access_token);
        $this->accessToken = $access_token;

        $this->api = new API($access_token);
    }

    public function getAccountInfo()
    {
        if ($this->api == null) {
            throw new \Exception("Access token is not set");
        }

        return $this->api->accountInfo();
    }

    public function requestPayment()
    {
        // WARNING: debug code
        $this->api = new API(getenv("YANDEX_ACCESS_TOKEN"));


        if ($this->api == null) {
            throw new \Exception("Access token is not set");
        }

        $requestPayment = $this->api->requestPayment(array(
            "pattern_id" => "p2p",
            "to" => getenv('YANDEX_WALLET'),
            "amount_due" => "5",
            "comment" => "comment",
            "message" => "message",
            "label" => "label",
            "test_payment" => "true",
            "test_result" => "success",
        ));

        var_dump($requestPayment);

        return $requestPayment;
    }

    public function makePaymentFromWallet()
    {
        $requestPayment = $this->requestPayment();
        if($requestPayment->status != "success") {
            throw new \Exception($request->error);
        }

        do {
            $process_payment = $this->api->processPayment(array(
                "request_id" => $requestPayment->request_id,
                "test_payment" => "true",
                "test_result" => "success",
            ));
            if($process_payment->status == "in_progress") {
                sleep(2);
            }

            if($process_payment->status == "success")
            {
                $this->db->saveInvoiceId(123, $process_payment->payment_id);
                var_dump("process payment is success");
                var_dump($process_payment);
            }

        } while ($process_payment->status == "in_progress");

    }

    public function getInstanceId($user_id)
    {
        if ($this->db->userHasInstanceId($user_id)) {
            return $this->db->getInstanceId($user_id);
        } else {
            $response = ExternalPayment::getInstanceId($this->client_id);
            if ($response->status == "success") {
                $instance_id = $response->instance_id;
                $this->db->setUserInstanceId($user_id, $instance_id);
                return $instance_id;
            } else {
                // throw exception with $response->error message
                throw new \Exception($response->error);
            }
        }
    }

    public function makePayment($user_id, $amount)
    {
        $instance_id = $this->getInstanceId($user_id);
        $external_payment = new ExternalPayment($instance_id);

        $payment_options = array(
            "pattern_id" => "p2p",
            "to" => getenv('YANDEX_WALLET'),
            "amount" => $amount,
            "comment" => "sample test payment comment",
            "message" => "Оплата курса course_name",
            "test_payment" => "true",
            "test_result" => "success"
        );

        $response = $external_payment->request($payment_options);

        if ($response->status == "success") {
            $request_id = $response->request_id;

            $process_options = array(
                "request_id" => $request_id,
                "instance_id" => $this->getInstanceId($user_id),
                "ext_auth_success_uri" => "http://mih1984.beget.tech/ymc-success.php",
                "ext_auth_fail_uri" => "http://mih1984.beget.tech/ymc-fail.php",
                "test_payment" => "true",
                "test_result" => "success",
            );
            do {
                $result = $external_payment->process($process_options);
                if ($result->status == "in_progress") {
                    sleep(2);
                }
                if ($result->status == "ext_auth_required") {
                    $url = sprintf(
                        "%s?%s",
                        $result->acs_uri,
                        http_build_query($result->acs_params)
                    );
                    header('Location: '. $url);
                    die();
                }
                if ($result->status == "sucess") {
                    $this->db->saveInvoiceId($user_id, $result->invoice_id);
                    echo "payment was sucess";
                }
            } while ($result->status == 'in_progress');
        } else {
            // throw exception with $response->message
            throw new \Exception($response->message);
        }
    }
}
