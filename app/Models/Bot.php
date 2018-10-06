<?php
namespace App\Models;

use App\Factories\MenuFactory;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use unreal4u\TelegramAPI\Telegram\Methods\EditMessageText;

use unreal4u\TelegramAPI\TgLog;
use unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
use unreal4u\TelegramAPI\Telegram\Methods\SendMessage;
use unreal4u\TelegramAPI\Telegram\Types\CallbackQuery;
use App\Controllers\MenuController;

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

    /**
     *
     * @var int
     */
    private $inlineKeyboardMessageId = null;

    final public function __construct()
    {
        $this->token = getenv('BOT_TOKEN');
        $this->loop = Factory::create();
        $this->tgLog = new TgLog($this->token, new HttpClientRequestHandler($this->loop));
        $this->getUpdates = new GetUpdates();
    }

    /**
     * Start bot function
     *
     * @method start
     */
    public function start()
    {
        while (true) {
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

        if($request === '/start') {
            $menu = MenuFactory::build('MainMenu', $chatId);
            $this->showMenu($menu);
        }

        // switch ($request) {
        //     case '/start':
        //         $this->sendMessage($chat_id, $this->messages['greeting']);
        //         break;
        //     case '/courses':
        //         $this->sendMessage($chat_id, null, 'mainMenu');
        //         break;
        //     default:
        //         break;
        // }
    }

    private function handleCallbackQueryUpdate(CallbackQuery $callbackQuery)
    {
        $menuController = new MenuController($callbackQuery, $this);

        $editMessageText = $menuController->getMenu();

        $promise = $this->tgLog->performApiRequest($editMessageText);

        $promise->then(function ($response) {
            $this->inlineKeyboardMessageId = $response->message_id;
        }, function (\Exception $exception) {
            echo 'Exception ' . get_class($exception) . ' caught, message: ' . $exception->getMessage();
        });
        $this->loop->run();
    }

    /**
     *
     * @method sendMessage
     * @param int $chat_id
     * @param string $message
     */
    public function sendMessage($chat_id, $message = null, $keyboard = null)
    {
        $sendMessage = new SendMessage();
        $sendMessage->chat_id = $chat_id;
        if ($message != null) {
            $sendMessage->text = $message;
        }
        $isInlineKeyboard = false;

        if ($keyboard != null) {
            $sendMessage = MenuController::appendMenuToMessage($sendMessage, $keyboard);
            $isInlineKeyboard = true;
        }

        $promise = $this->tgLog->performApiRequest($sendMessage);

        $promise->then(function ($response) {
            global $isInlineKeyboard;
            if ($isInlineKeyboard) {
                $this->inlineKeyboardMessageId = $response->message_id;
            }
        }, function (\Exception $exception) {
            echo 'Exception ' . get_class($exception) . ' caught, message: ' . $exception->getMessage();
        });
    }

    private function showMenu($menu) {
        $promise = $this->tgLog->performApiRequest($menu);
    }
}
