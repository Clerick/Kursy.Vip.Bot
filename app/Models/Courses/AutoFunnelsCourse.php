<?php
namespace App\Models\Courses;

use App\Models\BaseCourse;

class AutoFunnelsCourse extends BaseCourse
{
    protected function setName()
    {
        $this->name = "Автоворонки в мессенжерах";
    }

    protected function setDescription()
    {
        $this->description = "Автоворонки в мессенжерах - Кир Уланов (2018г.)\n" .
            $this->getPrice() . "р";
    }

    protected function setContent()
    {
        $this->content = "1. Как отстроиться от конкурентов и получать в 2-3 раза больше заказов\n2. Продающая линейка продуктов. Какими должны быть ваши продукты\n3.Создание собственной прибыльной стратегии автоворонки\n4.Разбор плана проекта автоворонок\n5.Как писать, чтобы у вас покупали. Копирайтинг для автоворонок\n6.Настройка рекламы и запуск трафик-системы\n7.Техническая реализация воронки в мессенджерах\n \nБонус\nКак собрать команду мечты\nКонструктор email-воронки";
    }

    protected function setPrice()
    {
        $this->price = 3900;
    }
}
