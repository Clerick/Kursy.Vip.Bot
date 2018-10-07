<?php
namespace App\Models;

use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Methods\EditMessageText;

abstract class BaseMenu
{
    /**
     *   @var EditMessageText|SendMessage
     */
    protected $menuText;

    /**
     *   @var string
     */
    protected $menuParam;

    public function __construct(int $chatId, int $messageId = null)
    {
        if ($messageId === null) {
            $this->menuText = new SendMessage();
            $this->menuText->chat_id = $chatId;
        } else {
            $this->menuText = new EditMessageText();
            $this->menuText->chat_id = $chatId;
            $this->menuText->message_id = $messageId;
            $this->menuText->parse_mode = 'Markdown';
            $this->menuText->disable_web_page_preview = true;
        }

        $this->setText();
        $this->setReplyMarkup();
    }

    /**
     *   @method get
     *   @return SendMessage|EditMessageText
     */
    public function get()
    {
        return $this->menuText;
    }

    /**
     *   Set $menuText->text property
     */
    abstract protected function setText();

    /**
     *   Set $menuText->reply_markup property
     */
    abstract protected function setReplyMarkup();
}
