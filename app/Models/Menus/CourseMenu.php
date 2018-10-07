<?php
namespace App\Models\Menus;

use App\Models\BaseMenu;
use App\Models\BaseCourse;
use App\Factories\CourseFactory;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Keyboard\Markup;

class CourseMenu extends BaseMenu
{
    /**
     *   @var BaseCourse
     */
    private $course;

    public function __construct(int $chatId, int $messageId = null, string $courseClassName)
    {
        if ($courseClassName === null) {
            throw new \Exception("Cant create CourseMenu- courseClassName is empty");
        }
        $this->course = CourseFactory::build($courseClassName);
        parent::__construct($chatId, $messageId);
    }

    protected function setText()
    {
        $this->menuText->text = $this->course->getDescription();
    }

    protected function setReplyMarkup()
    {
        $buyButton = [
            "text" => "Купить",
            "url" => "https://t.me/" . getenv('ADMIN'),
        ];

        $warrantyButton = [
            "text" => "Гарантии",
            "callback_data" => "warranty-" . $this->course->getShortName(),
        ];

        $contentButton = [
            "text" => "Содержание",
            "callback_data" => "content-" . $this->course->getShortName(),
        ];

        $backButton = [
            "text" => "Назад",
            "callback_data" => "mainMenu",
        ];

        $inlineKeyboard = new Markup([
            "inline_keyboard" => [
                [
                    $buyButton,
                ],
                [
                    $warrantyButton,
                ],
                [
                    $contentButton,
                    $backButton,
                ],
            ],
        ]);

        $this->menuText->reply_markup = $inlineKeyboard;
    }
}
