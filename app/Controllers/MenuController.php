<?php
namespace App\Controllers;

use App\Factories\MenuFactory;
use unreal4u\TelegramAPI\Telegram\Types\CallbackQuery;
use unreal4u\TelegramAPI\Telegram\Methods\EditMessageText;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;

class MenuController
{
    /**
     *   @var CallbackQuery
     */
    private $callbackQuery;

    /**
     *   @var string
     */
    private $menuType;

    /**
     *   @var string
     */
    private $menuParam;

    public function __construct(CallbackQuery $callbackQuery)
    {
        $this->callbackQuery = $callbackQuery;
        $this->setParams($callbackQuery);
    }

    /**
     *   @method getChatId
     *   @return int
     */
    private function getChatId() : int
    {
        return $this->callbackQuery->message->chat->id;
    }

    /**
     *   @method getMessageId
     *   @return int
     */
    private function getMessageId()
    {
        return $this->callbackQuery->message->message_id;
    }

    /**
     *   @method setParams
     *   @param  CallbackQuery $callbackQuery
     */
    private function setParams($callbackQuery)
    {
        $params = explode('-', $callbackQuery->data);

        $this->menuType = $params[0];
        if (key_exists(1, $params)) {
            $this->menuParam = $params[1];
        }
    }

    /**
     *   @method getMenu
     *   @return SendMessage|EditMessageText
     */
    public function getMenu()
    {
        $chatId = $this->getChatId();
        $messageId = $this->getMessageId();
        switch ($this->menuType) {
            case 'mainMenu':
                $menu = MenuFactory::build('mainMenu', $chatId, $messageId);
                break;

            case 'course':
                $menu = MenuFactory::build('CourseMenu', $chatId, $messageId, $this->menuParam);
                break;

            case 'warranty':
                $menu = MenuFactory::build('WarrantyMenu', $chatId, $messageId, $this->menuParam);
                break;

            case 'content':
                $menu = MenuFactory::build('ContentMenu', $chatId, $messageId, $this->menuParam);
                break;

            default:
                break;
        }
        return $menu;
    }
}
