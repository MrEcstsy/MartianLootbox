<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\listeners;

use ecstsy\MartianLootbox\Loader;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\utils\TextFormat as C;

final class EventListener implements Listener {

    public function onBlockPlace(BlockPlaceEvent $event): void {
        $item = $event->getItem();
        $tag = $item->getNamedTag();

        if ($tag->getTag("MartianLootbox") !== null) {
            $event->cancel();
        }
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $tag = $item->getNamedTag();
        $lootboxTag = $tag->getCompoundTag("MartianLootbox");

        if ($lootboxTag === null || !$lootboxTag->getTag("lootbox")) {
            return;
        }

        $lootboxId = $lootboxTag->getString("lootbox");
        $lootboxConfig = Loader::getLootboxManager()->getLootboxConfiguration($lootboxId);

        if (empty($lootboxConfig['rewards']) || !is_array($lootboxConfig['rewards'])) {
            $player->sendMessage(C::colorize(Loader::getLanguageManager()->getNested('error.no-rewards')));
            return;
        }  
        
        if ($player->isSneaking()) {
            Loader::getLootboxManager()->previewLootbox($player, $lootboxId);
            return;
        }

        Loader::getLootboxManager()->openLootbox($player, $lootboxId);   
        $item->pop();
        $player->getInventory()->setItemInHand($item);     
    }
}
