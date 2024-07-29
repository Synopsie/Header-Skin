<?php
declare(strict_types=1);

namespace skin\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use skin\Main;
use skin\skins\SkinSave;

class PlayerJoinListener implements Listener {

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        imagepng(SkinSave::skinDataToImage($player->getSkin()->getSkinData()), Main::getInstance()->getDataFolder() . "skins/" . $player->getName() . ".png");
        SkinSave::savePlayerHead($player->getName(), $player->getSkin()->getSkinData(), Main::getInstance()->getDataFolder() . "skins/heads/" . $player->getName() . ".png");
    }

}