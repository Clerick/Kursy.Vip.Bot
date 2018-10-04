<?php
namespace App\Controllers;

use App\Models\Bot;
use App\Models\KeyboardLayout;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Methods\EditMessageText;
use unreal4u\TelegramAPI\Telegram\Types\CallbackQuery;

class MenuController
{
    /**
     *   @var Bot
     */
    private $bot;

    /**
     *   @var CallbackQuery
     */
    private $callbackQuery;

    /**
     *   Command to show menu
     *   action tells what keyboard layout shoud be display
     *   target tells what course is belongs this keyboard layout
     *
     *   @var array
     */
    private $commands;

    public function __construct(CallbackQuery $callbackQuery, Bot $bot)
    {
        $this->callbackQuery = $callbackQuery;
        $this->setCommands($callbackQuery);
        $this->bot = $bot;
    }

    private function getChatId()
    {
        return $this->callbackQuery->message->chat->id;
    }

    private function getMessageId()
    {
        return $this->callbackQuery->message->message_id;
    }

    private function getUserId()
    {
        return $this->callbackQuery->from->username;
    }

    private function setCommands($callbackQuery)
    {
        $commands = explode('-', $callbackQuery->data);

        $commands['action'] = $commands[0];
        unset($commands[0]);

        if (array_key_exists(1, $commands)) {
            $commands['target'] = $commands[1];
            unset($commands[1]);
        }

        if (array_key_exists(2, $commands)) {
            $commands['courseName'] = $commands[2];
            unset($commands[2]);
        }

        $this->commands = $commands;
    }

    public static function appendMenuToMessage(SendMessage $sendMessage, string $menuName)
    {
        if (array_key_exists($menuName, KeyboardLayout::$menuHint)) {
            $sendMessage->text = KeyboardLayout::$menuHint[$menuName];
        } else {
            throw new \OutOfBoundsException("There is no such menu like $menuName");
        }

        $sendMessage->disable_web_page_preview = true;
        $sendMessage->parse_mode = 'Markdown';
        $sendMessage->reply_markup = KeyboardLayout::mainMenu();

        return $sendMessage;
    }

    public function getMenu()
    {
        switch ($this->commands['action']) {
            case 'course':
                $editMessageText = $this->courseMenu($this->commands['target']);
                break;

            case 'warranty':
                $editMessageText = $this->warrantyMenu($this->commands['target']);
                break;

            case 'content':
                $editMessageText = $this->contentMenu($this->commands['target']);
                break;

            case 'back':
                if ($this->commands['target'] === 'mainMenu') {
                    $editMessageText = $this->mainMenu();
                    break;
                }

                if($this->commands['target'] === 'course') {
                    $editMessageText = $this->courseMenu($this->commands['courseName']);
                    break;
                }

                throw new \Exception("Unknown back action");
                break;

            default:
                throw new \Exception("Unknown menu action");
                break;
        }

        return $editMessageText;
    }

    private function prepareText() : EditMessageText
    {
        $editMessageText = new EditMessageText();
        $editMessageText->chat_id =  $this->getChatId();
        $editMessageText->message_id = $this->getMessageId();
        $editMessageText->parse_mode = 'Markdown';
        $editMessageText->disable_web_page_preview = true;

        return $editMessageText;
    }

    private function courseMenu($courseName)
    {
        $editMessageText = $this->prepareText();
        $editMessageText->text = KeyboardLayout::$menuHint['course'][$courseName];
        $editMessageText->reply_markup =
            KeyboardLayout::courseMenu($this->commands['target']);

        return $editMessageText;
    }

    private function contentMenu($courseName)
    {
        $editMessageText = $this->prepareText();
        $editMessageText->text = KeyboardLayout::$menuHint['content'][$courseName];
        $editMessageText->reply_markup =
            KeyboardLayout::backToCourse($courseName);

        return $editMessageText;
    }

    private function warrantyMenu($courseName)
    {
        $editMessageText = $this->prepareText();
        $editMessageText->text = KeyboardLayout::$menuHint['warranty']['text'];
        $editMessageText->reply_markup =
            KeyboardLayout::warrantyMenu($courseName);

        return $editMessageText;
    }

    private function mainMenu()
    {
        $editMessageText = $this->prepareText();
        $editMessageText->text = KeyboardLayout::$menuHint['mainMenu'];

        $editMessageText->reply_markup = KeyboardLayout::mainMenu();

        return $editMessageText;
    }
}
