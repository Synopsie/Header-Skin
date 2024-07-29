<?php
declare(strict_types=1);

namespace skin\utils;

use JsonException;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use skin\Main;

class Utils {

    public static function getHeadItem($skin, $name = null, $count = 1): Item {
        $skin = $skin instanceof Skin ? self::skinToTag($skin) : $skin;
        $item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER())->asItem();
        $tag = $item->getCustomBlockData() ?? new CompoundTag();
        $tag->setTag('skin', $skin);
        $tag->setString('player', $name);
        $item->setCustomBlockData($tag);
        $item->setCount($count);
        $item->setCustomName(str_replace('%player%', $name ?? $skin->getString('name', 'player'), Main::getInstance()->getConfig()->get('head.name')));
        return $item;
    }

    private static function skinToTag(Skin $skin): CompoundTag {
        return (new CompoundTag())->setString('name', $skin->getSkinId())->setByteArray('data', $skin->getSkinData());
    }

    /**
     * @throws JsonException
     */
    public static function tagToSkin(CompoundTag $tag): Skin {
        return new Skin(
            $tag->getString('name'),
            $tag->getByteArray('data')
        );
    }

}