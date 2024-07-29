<?php
declare(strict_types=1);

namespace skin\command;

use iriss\CommandBase;
use iriss\parameters\IntParameter;
use iriss\parameters\StringParameter;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use skin\Main;
use skin\skins\SkinSave;
use skin\utils\Utils;

class GiveHeadCommand extends CommandBase {

    public function __construct(string $name, Translatable|string $description, string $usageMessage, array $subCommands = [], array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $subCommands, $aliases);
        $this->setPermission(Main::getInstance()->getConfig()->getNested('permission.name', 'givehead.use'));
    }

    public function getCommandParameters() : array {
        return [
            new StringParameter('player'),
            new IntParameter('count', isOptional: true)
        ];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        $config = Main::getInstance()->getConfig();
        if(!$sender instanceof Player) {
            $sender->sendMessage($config->get('use.command.in.game'));
            return;
        }

        $player = $parameters['player'];
        $path = Main::getInstance()->getDataFolder() . "skins/" . $player . '.png';
        if(!file_exists($path)) {
            $sender->sendMessage($config->get('player.skin.not.found'));
            return;
        }

        $amount = $parameters['count'] ?? 1;
        $item = Utils::getHeadItem(SkinSave::imageToSkinData($path), $player, $amount);
        if($sender->getInventory()->canAddItem($item)) {
            $sender->getInventory()->addItem($item);
        }else{
            $sender->sendMessage($config->get('inventory.full'));
        }
    }
}