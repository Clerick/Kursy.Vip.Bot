<?php
namespace App\Factories;

use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Methods\EditMessageText;

class MenuFactory
{
    /**
     *   @method build
     *   @param  string $menuName
     *   @param  int    $chatId
     *   @param  int    $messageId
     *   @param  string $menuParam
     *   @return SendMessage|EditMessageText
     */
    public static function build(string $menuName, int $chatId, int $messageId = null, string $menuParam = null)
    {
        $fullMenuClassName = "\\App\\Models\\Menus\\$menuName";
        if (!class_exists($fullMenuClassName)) {
            throw new \Exception("Cant find $menuName class in \\App\\Models\\Menus namespace");
        }
        $menu = new $fullMenuClassName($chatId, $messageId, $menuParam);
        return $menu->get();
    }
}
