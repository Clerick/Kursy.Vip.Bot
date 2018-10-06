<?php
namespace App\Models;

use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Methods\EditMessageText;

abstract class BaseMenu {
    /**
     *   @var EditMessageText|SendMessage
     */
    protected $menuText;

    public function __construct($chatId, $messageId) {
        if($messageId === null) {
            $this->menuText = new SendMessage();
            $this->menuText->chat_id = $chatId;
        } else {
            $this->menuText = new EditMessageText();
            $this->menuText->chat_id = $chatId;
            $this->menuText->message_id = $messageId;
            $this->menuText->parse_mode = 'Markdown';
            $this->menuText->disable_web_page_preview = true;
        }
    }

    public function get()
    {
        return $this->menuText;
    }
}
