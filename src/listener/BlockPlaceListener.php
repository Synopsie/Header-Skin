<?php
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
        if ($event->isCancelled() || ($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())->getCompoundTag('skin') === null) return;
        $event->cancel();
        foreach ($event->getTransaction()->getBlocks() as $entry) {
            [, , , $block] = $entry;
            Utils::spawnHead(
                ($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())
                    ->getCompoundTag('skin'),
                ($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())
                    ->getString('Player', ($event->getPlayer()->getInventory()->getItemInHand()->getCustomBlockData() ?? new CompoundTag())
                        ->getCompoundTag('skin')->getString('name')),
                $block->getPosition(), Utils::getYaw($block->getPosition(),
                    $event->getPlayer()->getPosition())
            );
            if (!$event->getPlayer()->isCreative()) {
                $item = $event->getPlayer()->getInventory()->getItemInHand();
                $item->pop();
                $event->getPlayer()->getInventory()->setItemInHand($item);
            }
        }
    }

}