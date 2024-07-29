<?php
declare(strict_types=1);

namespace skin\command;

use iriss\parameters\IntParameter;
use iriss\parameters\StringParameter;
use JsonException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use skin\Main;
use skin\skins\SkinSave;
use skin\utils\Utils;

class GiveHeadCommand extends Command {

    public function __construct(string $name, Translatable|string $description, string $usageMessage, array $subCommands = [], array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $subCommands, $aliases);
        $this->setPermission(Main::getInstance()->getConfig()->getNested('permission.name', 'givehead.use'));
    }

    /**
     * @throws JsonException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $parameters) : void {
        $config = Main::getInstance()->getConfig();
        if(!$sender instanceof Player) {
            $sender->sendMessage($config->get('use.command.in.game'));
            return;
        }

        $player = $parameters[0];
        $path = Main::getInstance()->getDataFolder() . "skins/" . $player . '.png';
        if(!file_exists($path)) {
            $sender->sendMessage($config->get('player.skin.not.found'));
            return;
        }
        if(!isset($parameters[1])) {
            $amount = 1;
        }else{
            $amount = (int)$parameters[1];
        }

        if(($online = Server::getInstance()->getPlayerExact($player)) instanceof Player) {
            $skin = $online->getSkin();
        }else{
            $skin = SkinSave::getSkin($path);
        }
        $item = Utils::getHeadItem($skin, $player, $amount);
        if($sender->getInventory()->canAddItem($item)) {
            $sender->getInventory()->addItem($item);
        }else{
            $sender->sendMessage($config->get('inventory.full'));
        }
    }
}