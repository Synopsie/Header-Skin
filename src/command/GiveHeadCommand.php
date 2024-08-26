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
 * @version 2.2.1
 *
 */

declare(strict_types=1);

namespace skin\command;

use iriss\CommandBase;
use iriss\parameters\IntParameter;
use iriss\parameters\PlayerParameter;
use iriss\parameters\StringParameter;
use JsonException;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use skin\Main;
use skin\skins\SkinSave;
use skin\utils\Utils;
use function file_exists;

class GiveHeadCommand extends CommandBase {
	public function __construct(string $name, string|Translatable $description, string $usageMessage, array $subCommands = [], array $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $subCommands, $aliases);
		$this->setPermission(Main::getInstance()->getConfig()->getNested('command.permission.name', 'givehead.use'));
	}

	public function getCommandParameters() : array {
		return [
			new StringParameter('player'),
			new PlayerParameter('target', true),
			new IntParameter('amount', isOptional: true)
		];
	}

	/**
	 * @throws JsonException
	 */
	public function onRun(CommandSender $sender, array $parameters) : void {
		$config = Main::getInstance()->getConfig();
		if(!$sender instanceof Player) {
			$sender->sendMessage($config->get('use.command.in.game'));
			return;
		}

		$player = $parameters['player'];
		if(!file_exists(Main::getInstance()->getDataFolder() . "skins/" . $player . '.png')) {
			$sender->sendMessage($config->get('player.skin.not.found'));
			return;
		}
		if(!isset($parameters['amount'])) {
			$amount = 1;
		} else {
			$amount = (int) $parameters['amount'];
		}

		if(($online = Server::getInstance()->getPlayerExact($player)) instanceof Player) {
			$skin = $online->getSkin();
		} else {
			$skin = SkinSave::getSkin($player);
		}
		$item   = Utils::getHeadItem($skin, $player, $amount);
		$target = $sender;
		if(isset($parameters['target'])) {
			$target = Server::getInstance()->getPlayerExact($parameters['target']);
		}
		if($target === null) {
			$sender->sendMessage($config->get('player.not.found'));
			return;
		}
		if($target->getInventory()->canAddItem($item)) {
			$target->getInventory()->addItem($item);
		} else {
			$target->sendMessage($config->get('inventory.full'));
			$sender->sendMessage($config->get('inventory.full'));
		}
	}
}
