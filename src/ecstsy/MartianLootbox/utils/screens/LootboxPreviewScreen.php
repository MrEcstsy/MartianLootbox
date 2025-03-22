<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils\screens;

use ecstsy\MartianLootbox\Loader;
use ecstsy\MartianUtilities\interfaces\ScreenInterface;
use ecstsy\MartianUtilities\utils\InventoryUtils;
use ecstsy\MartianUtilities\utils\ItemUtils;
use ecstsy\MartianUtilities\utils\PlayerUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;

final class LootboxPreviewScreen implements ScreenInterface {

    private static InvMenu $menu;

    private const BORDER_SLOTS = [
        // Top row (0-8) and bottom row (45-53)
        0, 1, 2, 3, 4, 5, 6, 7, 8,
        45, 46, 47, 48, 49, 50, 51, 52, 53,
        // Left and right columns (excluding corners already set above)
        9, 18, 27, 36,
        17, 26, 35, 44
    ];

    private static ?Item $previousPageItem = null;
    private static ?Item $nextPageItem = null;
    private static ?Item $fillerItem = null;
    
    /** @var array */
    private array $previewConfig; 
    private array $settings;
    /** @var Item[] */
    private array $rewards;
    /** @var Item[] */
    private array $bonusRewards;
    private int $page;

    public function __construct(array $settings, array $rewards, array $bonusRewards, ?array $previewConfig = null)
    {
        $this->settings = $settings;
        $this->rewards = ItemUtils::setupItems($rewards);
        $this->bonusRewards = ItemUtils::setupItems($bonusRewards);
        $this->page = 1;

        $this->previewConfig = $previewConfig;
        self::initializeItems($this->previewConfig);
        self::$menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);

        self::$menu->setName(C::colorize("&r&8Viewing Loot"));
        self::$menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) {
            $player = $transaction->getPlayer();
            $clicked = $transaction->getItemClicked();
            $allItems = array_merge($this->rewards, $this->bonusRewards);
            $itemsPerPage = 28; 
            $totalItems = count($allItems);
            $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $itemsPerPage) : 1;
            
            if ($clicked->equalsExact(self::$previousPageItem) && $this->page > 1) {
                $this->page--;
                PlayerUtils::playSound($player, "item.book.page_turn");
                $this->setPage($this->page);
                return;
            }
            if ($clicked->equalsExact(self::$nextPageItem) && $this->page < $totalPages) {
                $this->page++;
                PlayerUtils::playSound($player, "item.book.page_turn");
                $this->setPage($this->page);
                return;
            }
        }));

        $this->populateMenuWithRewards(self::$menu->getInventory());
    }


    public function display(Player $player): void
    {
        self::$menu->send($player);
    }
    
    public function getInventory(): Inventory {
        return self::$menu->getInventory();
    }

    /**
     * Initialize border and navigation items from preview config.
     */
    private static function initializeItems(array $previewConfig): void {
        if (self::$fillerItem === null) {
            if (isset($previewConfig['items']['filler'])) {
                self::$fillerItem = StringToItemParser::getInstance()->parse($previewConfig['items']['filler']);
            } else {
                self::$fillerItem = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK())->asItem();
            }
        }
        
        if (self::$previousPageItem === null) {
            if (isset($previewConfig['navigation']['back']['material'])) {
                self::$previousPageItem = StringToItemParser::getInstance()->parse($previewConfig['navigation']['back']['material']);
                self::$previousPageItem->setCustomName(C::colorize($previewConfig['navigation']['back']['name'] ?? "&r&c&lBack"));
                if (isset($previewConfig['navigation']['back']['lore'])) {
                    self::$previousPageItem->setLore($previewConfig['navigation']['back']['lore']);
                }
            } else {
                self::$previousPageItem = VanillaItems::DYE()->setColor(DyeColor::RED())->setCustomName(C::colorize("&r&c&lBack"));
            }
        }
        
        if (self::$nextPageItem === null) {
            if (isset($previewConfig['navigation']['next']['material'])) {
                self::$nextPageItem = StringToItemParser::getInstance()->parse($previewConfig['navigation']['next']['material']);
                self::$nextPageItem->setCustomName(C::colorize($previewConfig['navigation']['next']['name'] ?? "&r&c&lNext"));
                if (isset($previewConfig['navigation']['next']['lore'])) {
                    self::$nextPageItem->setLore($previewConfig['navigation']['next']['lore']);
                }
            } else {
                self::$nextPageItem = VanillaItems::DYE()->setColor(DyeColor::LIME())->setCustomName(C::colorize("&r&c&lNext"));
            }
        }
    }
    
    private function populateMenuWithRewards(Inventory $inventory): void {        
        InventoryUtils::fillBorders($inventory, self::$fillerItem);
        
        $nonBorderSlots = [];
        for ($i = 0; $i < 54; $i++) {
            if (!in_array($i, self::BORDER_SLOTS, true)) {
                $nonBorderSlots[] = $i;
            }
        }
        
        $allItems = [];

        foreach ($this->rewards as $item) {
            $allItems[] = $item;
        }

        foreach ($this->bonusRewards as $item) {
            $lores = $item->getLore();
            $lores[] = C::colorize("&r&6&lBonus Loot");
            $item->setLore($lores);
            $allItems[] = $item;
        }
        
        $itemsPerPage = 28; 
        $totalItems = count($allItems);
        $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $itemsPerPage) : 1;
        
        $start = ($this->page - 1) * $itemsPerPage;
        $pagedItems = array_slice($allItems, $start, $itemsPerPage);
        
        $i = 0;
        foreach ($pagedItems as $item) {
            if (isset($nonBorderSlots[$i])) {
                $inventory->setItem($nonBorderSlots[$i], $item);
            }
            $i++;
        }
        
        if ($totalPages > 1) {
            if ($this->page > 1) {
                $inventory->setItem(47, self::$previousPageItem);
            }

            if ($this->page < $totalPages) {
                $inventory->setItem(51, self::$nextPageItem);
            }
        }
    }

    private function setPage(int $page): void {
        $inventory = $this->getInventory();
        $inventory->clearAll(); 
        
        foreach (self::BORDER_SLOTS as $slot) {
            $inventory->setItem($slot, self::$fillerItem);
        }
    
        $allItems = array_merge($this->rewards, $this->bonusRewards);
        $itemsPerPage = 28;
        $startIndex = ($page - 1) * $itemsPerPage;
        $pageItems = array_slice($allItems, $startIndex, $itemsPerPage);
        
        $itemSlot = 9;
        foreach ($pageItems as $item) {
            while (in_array($itemSlot, self::BORDER_SLOTS, true)) {
                $itemSlot++;
            }
            $inventory->setItem($itemSlot, $item);
            $itemSlot++;
        }
    
        $totalPages = max(1, (int)ceil(count($allItems) / $itemsPerPage));
        
        if ($page > 1) {
            $inventory->setItem(47, self::$previousPageItem);
        }
        if ($page < $totalPages) {
            $inventory->setItem(51, self::$nextPageItem);
        }
    }
}
