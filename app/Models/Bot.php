<?php
namespace App\Models;

use App\Factories\MenuFactory;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use unreal4u\TelegramAPI\Telegram\Methods\DeleteMessage;

use unreal4u\TelegramAPI\TgLog;
use unreal4u\TelegramAPI\Telegram\Methods\GetUpdates;
use unreal4u\TelegramAPI\HttpClientRequestHandler;
use unreal4u\TelegramAPI\Abstracts\TraversableCustomType;
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
            if(file_exists($stopFile)) {
                var_dump('file is exist');
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
        $promise = $this->tgLog->performApiRequest($menu);
    }
}
