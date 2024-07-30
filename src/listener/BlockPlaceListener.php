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
 * @version 2.0.1
 *
 */

declare(strict_types=1);

namespace skin\listener;

use JsonException;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\CompoundTag;
use skin\utils\Utils;

class BlockPlaceListener implements Listener {
	/**
	 * @throws JsonException
	 */
	public function onBlockPlace(BlockPlaceEvent $event) : void {
		if ($event->isCancelled() || ($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())->getCompoundTag('skin') === null) {
			return;
		}
		$event->cancel();
		foreach ($event->getTransaction()->getBlocks() as $entry) {
			[, , , $block] = $entry;
			Utils::spawnHead(
				($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())
					->getCompoundTag('skin'),
				($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())
					->getString('player', ($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())
						->getCompoundTag('skin')->getString('name')),
				$block->getPosition(),
				Utils::getYaw(
					$block->getPosition(),
					$event->getPlayer()->getPosition()
				)
			);
			if (!$event->getPlayer()->isCreative()) {
				$item = $event->getPlayer()->getInventory()->getItemInHand();
				$item->pop();
				$event->getPlayer()->getInventory()->setItemInHand($item);
			}
		}
	}

}
