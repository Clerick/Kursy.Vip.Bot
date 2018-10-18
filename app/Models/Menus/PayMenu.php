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
        $this->ym = new YMController();
        parent::__construct($chatId, $messageId);
    }

    protected function setText()
    {
        $this->menuText->text = "Стоимость курса " . $this->course->getPrice() . "руб.";
    }

    protected function setReplyMarkup()
    {
        $ymWalletButton = [
            "text" => "Оплатить с помощью кошелька ЯД",
            // "callback_data" => "ymw-" . $this->course->getShortName(),
            "url" => $this->ym->getAuthUrl(),
        ];

        $ymCardButton = [
            "text" => "Оплатить с помощью карточки",
            // "callback_data" => "ymw-" . $this->course->getShortName(),
            "url" => $this->ym->getAuthUrl(),
        ];

        $backButton = [
            "text" => "Назад",
            "callback_data" => "course-" . $this->course->getShortName(),
        ];

        $inlineKeyboard = new Markup([
            "inline_keyboard" => [
                [
                    $ymWalletButton,
                ],
                [
                    $backButton,
                ],
            ],
        ]);

        $this->menuText->reply_markup = $inlineKeyboard;
    }
}
