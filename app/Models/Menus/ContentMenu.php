<?php
namespace App\Models\Menus;

use App\Models\BaseMenu;
use App\Models\BaseCourse;
use App\Factories\CourseFactory;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Keyboard\Markup;

class ContentMenu extends BaseMenu
{
    /**
     *   @var BaseCourse
     */
    private $course;

    public function __construct(int $chatId, int $messageId = null, string $courseClassName)
    {
        if ($courseClassName === null) {
            throw new \Exception("Cant create ContentMenu- courseClassName is empty");
        }

        $this->course = CourseFactory::build($courseClassName);
        parent::__construct($chatId, $messageId);
    }

    protected function setText()
    {
        $this->menuText->text = $this->course->getContent();
    }

    protected function setReplyMarkup()
    {
        $backButton = [
            "text" => "Назад",
            "callback_data" => "course-" . $this->course->getShortName(),
        ];

        $inlineKeyboard = new Markup([
            "inline_keyboard" => [
                [
                    $backButton,
                ],
            ],
        ]);

        $this->menuText->reply_markup = $inlineKeyboard;
    }
}
