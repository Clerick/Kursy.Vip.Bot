<?php
namespace App\Factories;

use App\Models\Menus\MainMenu;

class MenuFactory {
    public static function build(string $menuName, $chatId, $messageId = null) {
        $menu = new MainMenu($chatId, $messageId);
        return $menu->get();
    }
}
