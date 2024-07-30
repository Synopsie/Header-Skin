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

namespace skin;

use Exception;
use iriss\IrissCommand;
use olymp\PermissionManager;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\DefaultPermissions;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use skin\command\GiveHeadCommand;
use skin\entity\HeadEntity;
use skin\listener\BlockPlaceListener;
use skin\listener\PlayerJoinListener;

use function file_exists;
use function mkdir;
use function var_dump;

class Main extends PluginBase {
	use SingletonTrait;

	protected function onLoad() : void {
		$this->getLogger()->info("§6Chargement du Header-Skin plugin...");

		self::setInstance($this);

		if(!file_exists($this->getDataFolder() . 'skins')) {
			@mkdir($this->getDataFolder() . 'skins');
			@mkdir($this->getDataFolder() . 'skins/heads');
		}

		$this->saveResource('config.yml');
	}

	/**
	 * @throws Exception
	 */
	protected function onEnable() : void {
		$config = $this->getConfig();
		require $this->getFile() . 'vendor/autoload.php';

		$groups = match($config->getNested('command.permission.default')) {
			'console' => DefaultPermissions::ROOT_CONSOLE,
			'op'      => DefaultPermissions::ROOT_OPERATOR,
			default   => DefaultPermissions::ROOT_USER
		};

		$permissionManager = new PermissionManager();
		$permissionManager->registerPermission($config->getNested('command.permission.name'), 'synopsie.header-skin', $groups);

		EntityFactory::getInstance()->register(HeadEntity::class, function (World $world, CompoundTag $nbt) : Entity {
			return new HeadEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
		}, ['Head', 'HeadEntity', 'PlayerHead']);

		$this->getServer()->getCommandMap()->register('Header-Skin', new GiveHeadCommand(
			$config->getNested('command.name', 'givehead'),
			$config->getNested('command.description', "Permet de vous donner la tête d'un joueur"),
			$config->getNested('command.usage', "/givehead <player>"),
			[],
			$config->getNested('command.alias', [])
		));

		$this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new BlockPlaceListener(), $this);
		IrissCommand::register($this);

		$this->getLogger()->info("§aHeader-Skin plugin activé avec succès !");
	}

}
