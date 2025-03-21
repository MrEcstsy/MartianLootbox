<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils;

use ecstsy\MartianLootbox\Loader;
use ecstsy\MartianLootbox\utils\inventory\CustomSizedInvMenuType;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\utils\TextFormat as C;

final class Utils {
    
    public static function initCustomSizedMenu(): void {
        $packet = StaticPacketCache::getInstance()->getAvailableActorIdentifiers();
        $tag = $packet->identifiers->getRoot();
        assert($tag instanceof CompoundTag);
        $idList = $tag->getListTag("idlist");
        assert($idList !== null);
        $idList->push(CompoundTag::create()
            ->setString("bid", "")
            ->setByte("hasspawnegg", 0)
            ->setString("id", CustomSizedInvMenuType::ACTOR_NETWORK_ID)
            ->setByte("summonable", 0));
    }

    public static function createLootboxItem(string $identifier, int $amount = 1): ?Item {
        $config = Loader::getLootboxManager()->getLootboxConfiguration($identifier);

        if ($config === null) {
            return null;
        }

        $materialString = $config["item"]['material'] ?? "CHEST";
        $name = $config["item"]['name'] ?? "Unnamed Lootbox";
        $lore = $config["item"]['lore'] ?? [];

        $material = StringToItemParser::getInstance()->parse($materialString);

        if ($material === null) {
            return null;
        }

        $material->setCount($amount);
        $material->setCustomName(C::colorize($name));

        $coloredLore = array_map(static fn($line) => C::colorize($line), $lore);
        $material->setLore($coloredLore);

        $root = $material->getNamedTag();
        $lootboxTag = new CompoundTag();

        $lootboxTag->setString("lootbox", $identifier);

        $root->setTag("MartianLootbox", $lootboxTag);

        return $material;
    }
}