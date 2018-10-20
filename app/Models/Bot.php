<?php

namespace App\Models;

use App\Factories\MenuFactory;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use App\Controllers\MenuController;
use unreal4u\TelegramAPI\TgLog;
use unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
use unreal4u\TelegramAPI\Telegram\Types\CallbackQuery;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;

class Bot
{

    /**
     *
     * @var LoopInterface
     */
    private $loop;

    /**
     *
     * @var TgLog
     */
    private $tgLog;

    /**
     *
     * @var GetUpdates
     */
    private $getUpdates;

    /**
     *
     * @var int
     */
    private $offset = null;

    /**
     *
     * @var string
     */
    private $token = '';

    final public function __construct()
    {
        $this->token = getenv('BOT_TOKEN');
        $this->loop = Factory::create();
        $this->tgLog = new TgLog($this->token, new HttpClientRequestHandler($this->loop));
        $this->getUpdates = new GetUpdates();
    }

    /**
     * Start bot function. To stop bot, create empty file "stop.txt" in /app folder
     *
     * @method start
     */
    public function start()
    {
        while (true) {
            $stopFile = dirname(__DIR__) . "/stop.txt";
            if (file_exists($stopFile)) {
                break;
            }
            sleep(2);

            if ($this->offset != null) {
                $this->getUpdates->offset = $this->offset;
            }

            $updatePromise = $this->tgLog->performApiRequest($this->getUpdates);
            $updatePromise->then(function (TraversableCustomType $updatesArray) {
                $this->handleUpdates($updatesArray);
            }, function (\Exception $exception) {
                // Onoes, an exception occurred...
                echo 'Exception ' . get_class($exception) . ' caught, message: ' . $exception->getMessage();
            });
            $this->loop->run();
        }
    }

    public function sendCourseLink($chatId, $link)
    {
        $message = "Спасибо за оплату. Курс вы можете скачать по " .
        "<a href=\"" . $link . "\">ссылке</a>";

        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chatId;
        $sendMessage->text = $message;
        $sendMessage->parse_mode = "HTML";
        $sendMessage->disable_web_page_preview = true;

        $this->tgLog->performApiRequest($sendMessage);
        $this->loop->run();
    }

    public function sendMessage($chatId, $message)
    {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chatId;
        $sendMessage->text = $message;
        $sendMessage->disable_web_page_preview = true;

        $this->tgLog->performApiRequest($sendMessage);
        $this->loop->run();
    }

    /**
     *
     * @method handleUpdates
     * @param TraversableCustomType $updatesArray
     */
    private function handleUpdates($updatesArray)
    {
        foreach ($updatesArray as $update) {
            $this->offset = $update->update_id + 1;

            if ($update->message != null) {
                $this->handleMessageUpdate($update);
            }

            if ($update->callback_query != null) {
                $this->handleCallbackQueryUpdate($update->callback_query);
            }
        }
    }

    private function handleMessageUpdate($update)
    {
        $chatId = $update->message->chat->id;
        $request = $update->message->text;

        if ($request === '/start') {
            $menu = MenuFactory::build('MainMenu', $chatId);
            $this->showMenu($menu);
        }
    }

    private function handleCallbackQueryUpdate(CallbackQuery $callbackQuery)
    {
        $menuController = new MenuController($callbackQuery);

        $menu = $menuController->getMenu();

        $this->showMenu($menu);
    }

    private function showMenu($menu)
    {
        $this->tgLog->performApiRequest($menu);
    }

}
