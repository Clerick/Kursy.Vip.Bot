<?php
namespace App\Models;

use unreal4u\TelegramAPI\Telegram\Types\Inline\Keyboard\Markup;

class KeyboardLayout
{
    public static $menuHint = [
        "mainMenu" => "Вот список курсов",
        "course" => [
            "1" => "Accel - курс по созданию онлайн школ (2018г.)\n7900руб.",
            "2" => "Инструментариум БМ\n3900руб.",
            "3" => "Автоворонки в мессенжерах - Кир Уланов (2018г.)\n4800руб.",
            "4" => "Реальный Ютуб БМ\n1800руб.",
            "5" => "Реальный Инстаграм 2,0\n1800руб.",
        ],
        "warranty" => [
            "text" => "В качестве гарантий, мы можем предоставить вам скриншоты из данного видеокурса. Для этого нажмите на кнопку 'Просмотреть'",
            "1" => "https://cloud.mail.ru/public/L2ev/ffkGkfJHD",
            "2" => "https://cloud.mail.ru/public/66U1/fhvogwHWJ",
            "3" => "https://cloud.mail.ru/public/9ifK/BWDpJo3Rd",
            "4" => "https://cloud.mail.ru/public/Mbki/p5HhRw4gL",
            "5" => "https://cloud.mail.ru/public/HayE/E1LASvCuc",
        ],
        "content" => [
            "1" => "Ваша поддержка на курсе мастер-майнд группы и бадди\nВводные видеоуроки\nГлоссарий для резидентов Акселератора\nМастер майнд (смотреть после уроков)\nРабота с отзывами (смотреть после уроков)\nУрок 1. Старт\nУрок 2. Готовимся к пуску\nУрок 3. Выбор ниши\nУрок 4. Работа с экспертом\nУрок 5. Формирование продукта\nУрок 6. Целевая аудитория\nУрок 7. Схема быстрого запуска\nУрок 8. Посадочные страницы, лэндинги\nУрок 9. Структура продающего вебинара\nУрок 10. Трафик\nУрок 11. Аналитика\nУрок 12. Команда\nУрок 13. Продажи",
            "2" => "Модуль 1: О платформе Бм Институт\nМодуль 2: Фундамент и упаковка",
            "3" => "1. Как отстроиться от конкурентов и получать в 2-3 раза больше заказов\n2. Продающая линейка продуктов. Какими должны быть ваши продукты",
            "4" => "Введение\nМодуль 1 первые шаги в ютубе",
            "5" => "Модуль 1\nУрок 1 О правилах курса",
        ],
    ];

    public static function mainMenu() : Markup
    {
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

        return $inlineKeyboard;
    }

    public static function courseMenu($courseName) : Markup
    {
        $inlineKeyboard = new Markup([
            'inline_keyboard' => [
                [
                    ['text' => 'Купить', 'url' => 'https://t.me/' . getenv('ADMIN')],
                ],
                [
                    ['text' => 'Гарантии', 'callback_data' => "warranty-$courseName"],
                ],
                [
                    ['text' => 'Содержание', 'callback_data' => "content-$courseName"],
                    ['text' => 'Назад', 'callback_data' => "back-mainMenu"],
                ],
            ]
        ]);

        return $inlineKeyboard;
    }

    public static function backToCourse($courseName) : Markup
    {
        $inlineKeyboard = new Markup([
            'inline_keyboard' => [
                [
                    ['text' => 'Назад', 'callback_data' => "back-course-$courseName"],
                ],
            ],
        ]);

        return $inlineKeyboard;
    }

    public static function warrantyMenu($courseName) : Markup
    {
        $inlineKeyboard = new Markup([
            'inline_keyboard' => [
                [
                    ['text' => 'Просмотреть', 'url' => self::$menuHint['warranty'][$courseName]],
                ],
                [
                    ['text' => 'Назад', 'callback_data' => "back-course-$courseName"],
                ],
            ],
        ]);

        return $inlineKeyboard;
    }
}
