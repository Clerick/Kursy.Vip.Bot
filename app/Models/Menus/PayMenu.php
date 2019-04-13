<?php

namespace App\Models\Menus;

use App\Models\BaseMenu;
use App\Models\BaseCourse;
use App\Factories\CourseFactory;
use App\Controllers\YMController;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Keyboard\Markup;

class PayMenu extends BaseMenu
{

    /**
     * @var BaseCourse
     */
    private $course;

    /**
     * @var YMController
     */
    private $ym;

    public function __construct(int $chatId, int $messageId = null, string $courseClassName)
    {
        if ($courseClassName === null) {
            throw new \Exception("Cant create CourseMenu- courseClassName is empty");
        }
        $this->course = CourseFactory::build($courseClassName);
        $this->ym = new YMController($chatId, $this->course);
        parent::__construct($chatId, $messageId);
    }

    protected function setText()
    {
        $this->menuText->text =
            "Стоимость курса "
            . $this->course->getPrice()
            . "руб.\n Пожалуйста, свяжитесь с нашим администратором для выбора подходящего варианта оплаты.";

//		var_dump($this->menuText->text);
    }

    protected function setReplyMarkup()
    {
//        $ymWalletButton = [
//            "text" => "Оплатить с помощью ЯД",
//            "url" => $this->ym->getYMWalletUrl(),
//        ];

//		var_dump($ymWalletButton);
//        $ymCardButton = [
//            "text" => "Оплатить другим способом",
        // "callback_data" => "ymw-" . $this->course->getShortName(),
//            "url" => $this->ym->getAuthUrl(),
//            "url" => "https://t.me/" . getenv('ADMIN'),
//        ];
        $adminButton = [
            "text" => 'Связаться с администратором',
            "url" => "https://t.me/" . getenv('ADMIN'),
        ];

        $backButton = [
            "text" => "Назад",
            "callback_data" => "course-" . $this->course->getShortName(),
        ];

        $inlineKeyboard = new Markup([
            "inline_keyboard" => [
//                [
//                    $ymWalletButton,
//                ],
                [
                    $adminButton,
                ],
                [
                    $backButton,
                ],
            ],
        ]);

        $this->menuText->reply_markup = $inlineKeyboard;
    }

}
