<?php
namespace App\Models\Courses;

use App\Models\BaseCourse;

class AccelCourse extends BaseCourse
{
    protected function setName()
    {
        $this->name = "Accel - курс по созданию онлайн школ";
    }

    protected function setDescription()
    {
        $this->description = "Accel - курс по созданию онлайн школ (2018г.)\n" .
            $this->getPrice() . "р";
    }

    protected function setContent()
    {
        $this->content = "Ваша поддержка на курсе мастер-майнд группы и бадди\nВводные видеоуроки\nГлоссарий для резидентов Акселератора\nМастер майнд (смотреть после уроков)\nРабота с отзывами (смотреть после уроков)\nУрок 1. Старт\nУрок 2. Готовимся к пуску\nУрок 3. Выбор ниши\nУрок 4. Работа с экспертом\nУрок 5. Формирование продукта\nУрок 6. Целевая аудитория\nУрок 7. Схема быстрого запуска\nУрок 8. Посадочные страницы, лэндинги\nУрок 9. Структура продающего вебинара\nУрок 10. Трафик\nУрок 11. Аналитика\nУрок 12. Команда\nУрок 13. Продажи";
    }

    protected function setPrice()
    {
        $this->price = 7900;
    }
}
