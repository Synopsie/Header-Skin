<?php
declare(strict_types=1);

namespace skin;

use Exception;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use skin\command\GiveHeadCommand;
use skin\entity\HeadEntity;
use skin\listener\BlockPlaceListener;
use skin\listener\PlayerJoinListener;

class Main extends PluginBase {
    use SingletonTrait;

    protected function onLoad() : void {
        $this->getLogger()->info("§6Chargement du Header-Skin plugin...");

        self::setInstance($this);

        if(!file_exists($this->getDataFolder() . 'skins')){
            @mkdir($this->getDataFolder() . 'skins');
            @mkdir($this->getDataFolder() . 'skins/heads');
        }

        $this->saveResource('config.yml', true);
    }

    private function type(string $match) : Permission {
        $consoleRoot  = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_CONSOLE));
        $operatorRoot = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_OPERATOR, '', [$consoleRoot]));
        $everyoneRoot = DefaultPermissions::registerPermission(new Permission(DefaultPermissions::ROOT_USER, ''), [$operatorRoot]);
        return match ($match) {
            'console' => $consoleRoot,
            'op' => $operatorRoot,
            default => $everyoneRoot
        };
    }

    /**
     * @throws Exception
     */
    protected function onEnable() : void {

        $config = $this->getConfig();

        $permission = new Permission($config->getNested('permission.name', 'givehead.use'));
        DefaultPermissions::registerPermission($permission, [$this->type($config->getNested('permission.default', 'everyone'))]);

        EntityFactory::getInstance()->register(HeadEntity::class, function (World $world, CompoundTag $nbt): Entity {
            return new HeadEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['Head', 'HeadEntity', 'PlayerHead']);

        $this->getServer()->getCommandMap()->register('Header-Skin', new GiveHeadCommand(
            $config->getNested('command.name','givehead'),
            $config->getNested('command.description',"Permet de vous donner la tête d'un joueur"),
            $config->getNested('command.usage',"/givehead <player>"),
            [],
            $config->getNested('command.alias', [])
        ));

        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BlockPlaceListener(), $this);

        $this->getLogger()->info("§aHeader-Skin plugin activé avec succès !");
    }
}