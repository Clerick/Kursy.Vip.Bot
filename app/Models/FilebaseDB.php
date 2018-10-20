<?php
namespace App\Models;

use \Filebase\Database;
use \Filebase\Document;
use App\Interfaces\DBInterface;

class FilebaseDB implements DBInterface {
    /**
     * @var Database
     */
    private $db;

    public function __construct()
    {
        $db_path = dirname(__FILE__, 2) . '/Database';

        $this->db = new Database([
            'dir' => $db_path,
            'pretty' => true,
            'safe_filename' => true,
        ]);
    }

    /**
     * @param mixed $user_id
     * @return Document|null
     */
    public function getInstanceId($user_id)
    {
        if($this->db->has($user_id)) {
            $user = $this->db->get($user_id);
            return $user->instance_id;
        }
        return null;
    }

     public function getChatAccessToken($chat_id)
     {
         if($this->db->has($chat_id)) {
             $chat = $this->db->get($chat_id);
             return $chat->access_token;
         }
         return null;
     }

    /**
     * @param mixed $user_id
     * @param mixed $instance_id
     * @return Document
     */
    public function setUserInstanceId($user_id, $instance_id)
    {
        $user = $this->db->get($user_id);
        $user->instance_id = $instance_id;
        $user->save();
        return $user->instance_id;
    }

     public function setChatAccessToken($chat_id, $access_token)
     {
         $chat = $this->db->get($chat_id);
         $chat->access_token = $access_token;
         $chat->save();
         return $chat->access_token;
     }

    public function userHasInstanceId($user_id)
    {
        if(!$this->db->has($user_id)) {
            return false;
        }
        if($this->db->get($user_id)->instance_id != null)
        {
            return true;
        }
        return false;

    }

    public function saveInvoiceId($user_id, $invoice_id)
    {
        $user = $this->db->get($user_id);
        $user->invoice_id = $invoice_id;
        $user->save();
    }

    public function savePaymentId($chat_id, $payment_id)
    {
        $chat = $this->db->get($chat_id);
        $chat->invoice_id = $payment_id;
        $chat->save();
    }

}