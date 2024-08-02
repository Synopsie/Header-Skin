<?php

/*
 *  ____   __   __  _   _    ___    ____    ____    ___   _____
 * / ___|  \ \ / / | \ | |  / _ \  |  _ \  / ___|  |_ _| | ____|
 * \___ \   \ V /  |  \| | | | | | | |_) | \___ \   | |  |  _|
 *  ___) |   | |   | |\  | | |_| | |  __/   ___) |  | |  | |___
 * |____/    |_|   |_| \_|  \___/  |_|     |____/  |___| |_____|
 *
 * Ce système permet de sauvegarder et d'obtenir l'apparence et la tête du joueur.
 * En outre, si vous le souhaitez, vous pouvez également obtenir un bloc représentant la tête du joueur.
 * Cela offre plus de personnalisation et d'options pour afficher les skins et les têtes dans le jeu.
 *
 * @author Synopsie
 * @link https://github.com/Synopsie
 * @version 2.0.3
 *
 */

declare(strict_types=1);

namespace skin\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use skin\Main;
use skin\skins\SkinSave;
use function imagepng;

class PlayerJoinListener implements Listener {
	public function onPlayerJoin(PlayerJoinEvent $event) : void {
		$player = $event->getPlayer();
		imagepng(SkinSave::skinDataToImage($player->getSkin()->getSkinData()), Main::getInstance()->getDataFolder() . "skins/" . $player->getName() . ".png");
		SkinSave::savePlayerHead($player->getName(), $player->getSkin()->getSkinData(), Main::getInstance()->getDataFolder() . "skins/heads/");
	}

}
