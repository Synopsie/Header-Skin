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

namespace skin\utils;

use JsonException;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\Position;
use skin\entity\HeadEntity;
use skin\Main;
use function atan2;
use function str_replace;
use const M_PI;

class Utils {
	public static function getHeadItem(Skin $skin, $name = null, $count = 1) : Item {
		$skin = self::skinToTag($skin);
		$item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER())->asItem();
		$tag  = $item->getCustomBlockData() ?? new CompoundTag();
		$tag->setTag('skin', $skin);
		$tag->setString('player', $name);
		$item->setCustomBlockData($tag);
		$item->setCount($count);
		$item->setCustomName(str_replace('%player%', $name ?? $skin->getString('name', 'player'), Main::getInstance()->getConfig()->get('head.name')));
		return $item;
	}

	private static function skinToTag(Skin $skin) : CompoundTag {
		return (new CompoundTag())->setString('name', $skin->getSkinId())->setByteArray('data', $skin->getSkinData());
	}

	/**
	 * @throws JsonException
	 */
	public static function tagToSkin(CompoundTag $tag) : Skin {
		return new Skin(
			$tag->getString('name'),
			$tag->getByteArray('data')
		);
	}

	public static function getYaw(Vector3 $pos, Vector3 $target) : float {
		$yaw = atan2($target->z - $pos->z, $target->x - $pos->x) / M_PI * 180 - 90;
		if ($yaw < 0) {
			$yaw += 360.0;
		}
		foreach ([45, 90, 135, 180, 225, 270, 315, 360] as $direction) {
			if ($yaw <= $direction) {
				return $direction;
			}
		}
		return $yaw;
	}

	/**
	 * @throws JsonException
	 */
	public static function spawnHead($skin, $name, Position $pos, $yaw = null, $pitch = null) : HeadEntity {
		if ($skin instanceof CompoundTag) {
			$skin = self::tagToSkin($skin);
		}
		$nbt = new CompoundTag();
		$nbt->setString('player', $name);
		$head = new HeadEntity(Location::fromObject($pos->add(0.5, 0, 0.5), $pos->getWorld(), $yaw ?? 0, $pitch ?? 0), $skin, $nbt);
		$head->spawnToAll();
		return $head;
	}
}
