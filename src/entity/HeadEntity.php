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
 * @version 2.1.0
 *
 */

declare(strict_types=1);

namespace skin\entity;

use JsonException;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use skin\Main;
use skin\utils\Utils;

class HeadEntity extends Human {
	const HEAD_GEOMETRY = '{"format_version": "1.12.0", "minecraft:geometry": [{"description": {"identifier": "geometry.player_head", "texture_width": 64, "texture_height": 64, "visible_bounds_width": 2, "visible_bounds_height": 4, "visible_bounds_offset": [0, 0, 0]}, "bones": [{"name": "Head", "pivot": [0, 24, 0], "cubes": [{"origin": [-4, 0, -4], "size": [8, 8, 8], "uv": [0, 0]}, {"origin": [-4, 0, -4], "size": [8, 8, 8], "inflate": 0.5, "uv": [32, 0]}]}]}]}';

	private string $player;

	public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null) {
		parent::__construct($location, $skin, $nbt);
	}

	/**
	 * @throws JsonException
	 */
	protected function initEntity(CompoundTag $nbt) : void {
		parent::initEntity($nbt);
		$this->player = $nbt->getString('player');
		$this->setMaxHealth(1);
		$this->setSkin(new Skin($this->skin->getSkinId(), $this->skin->getSkinData(), '', 'geometry.player_head', self::HEAD_GEOMETRY));
		$this->getXpManager()->setCanAttractXpOrbs(false);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo {
		return new EntitySizeInfo(0.3, 0.3);
	}

	public function hasMovementUpdate() : bool {
		return false;
	}

	public function attack(EntityDamageEvent $source) : void {
		if (!$source instanceof EntityDamageByEntityEvent) {
			return;
		}
		if (!$source->getDamager() instanceof Player) {
			return;
		}
		if ($source->getCause() !== EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
			return;
		}
		/** @var Player $player */
		$player = $source->getDamager();
		$block  = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER());
		$block->position($this->getWorld(), $this->getPosition()->getFloorX(), $this->getPosition()->getFloorY(), $this->getPosition()->getFloorZ());
		$event = new BlockBreakEvent($player, $block, $player->getInventory()->getItemInHand(), false, $this->getDrops());
		$event->call();
		if ($event->isCancelled()) {
			$source->cancel();
			return;
		}
		parent::despawnFromAll();
		parent::kill();
	}

	public function getDrops() : array {
		return [Utils::getHeadItem($this->getSkin(), $this->player)];
	}

	public function saveNBT() : CompoundTag {
		$nbt = parent::saveNBT();
		$nbt->setString('player', $this->player);
		return $nbt;
	}

    public function onCollideWithPlayer(Player $player) : void {
        if(Main::getInstance()->getConfig()->get('enable.collision')) {
            $player->knockBack($player->getPosition()->getX() - $this->getPosition()->getX(), $player->getPosition()->getZ() - $this->getPosition()->getZ(), Main::getInstance()->getConfig()->get('motion.force', 0.1));
        }

    }
}
