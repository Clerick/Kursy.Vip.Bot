<?php
namespace App\Models\Menus;

use App\Models\BaseMenu;
use unreal4u\TelegramAPI\Telegram\Types\Inline\Keyboard\Markup;

class MainMenu extends BaseMenu
{
    protected function setText()
    {
        $this->menuText->text = "Вот список курсов:";
    }

    protected function setReplyMarkup()
    {
        $coursesNames = $this->getCoursesNames();
        $buttonRows = [];

        foreach ($coursesNames as $shortName => $name) {
            $button = [
                "text" => $name,
                "callback_data" => "course-$shortName",
            ];
            $buttonRow = [];
            $buttonRow[] = $button;
            $buttonRows[] = $buttonRow;
        }

        $techSupportButtonRow = [
            [
                "text" => "Связаться с тех. поддержкой",
                "url" => "https://t.me/" . getenv('ADMIN'),
            ]
        ];
        $buttonRows[] = $techSupportButtonRow;

        $inlineKeyboard = new Markup([
            'inline_keyboard' => $buttonRows
        ]);

        $this->menuText->reply_markup = $inlineKeyboard;
    }

    /**
     *   Get array of courses Names from Models\Courses folder
     *   @method getCoursesNames
     *   @return array
     */
    private function getCoursesNames() : array
    {
        $pathToModelsFolder = dirname(__DIR__);
        $pathToCoursesFolder = $pathToModelsFolder . '/Courses';
        $courses = glob($pathToCoursesFolder . '/*.php');
        $names = [];

        foreach ($courses as $courseFile) {
            $courseClass = basename($courseFile, '.php');
            $fullCourseClassName = "\\App\\Models\\Courses\\$courseClass";
            if (class_exists($fullCourseClassName)) {
                $courseObject = new $fullCourseClassName();
                $shortName = $courseObject->getShortName();
                $names[$shortName] = $courseObject->getName();
            }
        }

        return $names;
    }
}
