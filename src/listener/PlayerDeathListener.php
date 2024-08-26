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
 * @version 2.2.0
 *
 */

declare(strict_types=1);

namespace skin\listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use skin\Main;
use skin\utils\Utils;
use function in_array;

class PlayerDeathListener implements Listener {
	public function onPlayerDeath(PlayerDeathEvent $event) : void {
		$player = $event->getPlayer();
		$cause  = $player->getLastDamageCause();

		if($cause instanceof EntityDamageByEntityEvent) {
			$damager = $cause->getDamager();
			if($damager instanceof Player) {
				$config = Main::getInstance()->getConfig();
				if(!in_array($player->getName(), $config->get('blacklist', []), true)) {
					if($damager->getInventory()->canAddItem(Utils::getHeadItem($player->getSkin()))) {
						$damager->getInventory()->addItem(Utils::getHeadItem($player->getSkin(), $player->getName()));
					}
				}
			}
		}
	}
}
