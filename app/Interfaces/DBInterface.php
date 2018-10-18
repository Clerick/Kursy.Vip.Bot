<?php
namespace App\Interfaces;

interface DBInterface {
    public function getInstanceId($user_id);
    public function setUserInstanceId($user_id, $instance_id);
    public function userHasInstanceId($user_id);
    public function saveInvoiceId($user_id, $invoice_id);
}