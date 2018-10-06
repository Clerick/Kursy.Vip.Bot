<?php
namespace App\Models\Menus;

use App\Models\BaseMenu;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Keyboard\Markup;

class MainMenu extends BaseMenu {
    public function __construct($chatId, $messageId)
    {
        parent::__construct($chatId, $messageId);
        $this->menuText->text = "Вот список курсов";
        $inlineKeyboard = new Markup([
            'inline_keyboard' => [
                [
                    ['text' => "Accel - курс по созданию\nонлайн школ", 'callback_data' => 'course-1'],
                ],
                [
                    ['text' => "Инструментариум БМ", 'callback_data' => 'course-2'],
                ],
                [
                    ['text' => "Автоворонки в мессенжерах\nКир Уланов", 'callback_data' => 'course-3'],
                ],
                [
                    ['text' => "Реальный Ютуб БМ", 'callback_data' => 'course-4'],
                ],
                [
                    ['text' => "Реальный Инстаграм 2,0", 'callback_data' => 'course-5'],
                ],
                [
                    ['text' => "Связаться с техподдержкой", 'url' => 'https://t.me/' . getenv('ADMIN')],
                ],
            ]
        ]);

        $this->menuText->reply_markup = $inlineKeyboard;
    }
}
