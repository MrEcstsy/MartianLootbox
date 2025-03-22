<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils\screens;

use ecstsy\MartianLootbox\utils\inventory\CustomSizedInvMenu;
use ecstsy\MartianUtilities\interfaces\ScreenInterface;
use ecstsy\MartianUtilities\utils\ItemUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

final class ShuffleScreen implements ScreenInterface {

    private static InvMenu $menu;

    /** @var array<string, mixed> */
    private array $settings;
    /** @var Item[] */
    private array $rewards;
    /** @var Item[] */
    private array $bonusRewards;
    private array $givenRewards = [];
    
    public function __construct(array $settings, array $rewards, array $bonusRewards)
    {
        $this->settings = $settings;
        $this->rewards = ItemUtils::setupItems($rewards);
        $this->bonusRewards = ItemUtils::setupItems($bonusRewards);


        self::$menu = CustomSizedInvMenu::create(9);
        self::$menu->setName(C::colorize($this->settings['title'] ?? 'Lootbox Animation Type 1'));
        
        self::$menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction): void {

        }));

        $this->populateMenuWithRewards(self::$menu->getInventory());

        self::$menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) {
            if (!isset($this->givenRewards[$player->getName()])) {
                $this->giveRewardsOnClose($player, $inventory);
                $this->givenRewards[$player->getName()] = true; 
            }
        });
    }

    public function display(Player $player): void
    {
        self::$menu->send($player);
    }

    public function getInventory(): Inventory
    {
        return self::$menu->getInventory();
    }

    private function populateMenuWithRewards(Inventory $inventory): void
    {
        $rewardSlots = $this->settings['reward-slots'] ?? [];
        $bonusRewardSlots = $this->settings['bonus-rewards-slots'] ?? [];
    
        foreach ($this->rewards as $index => $reward) {
            if (isset($rewardSlots[$index])) {
                $slot = $rewardSlots[$index];
    
                if ($reward instanceof Item) {
                    $inventory->setItem($slot, $reward);
                } else {
                    throw new \InvalidArgumentException("Reward at index $index is not a valid Item.");
                }
            }
        }
    
        foreach ($this->bonusRewards as $index => $bonusReward) {
            if (isset($bonusRewardSlots[$index])) {
                $slot = $bonusRewardSlots[$index];
    
                if ($bonusReward instanceof Item) {
                    $inventory->setItem($slot, $bonusReward);
                } else {
                    throw new \InvalidArgumentException("Bonus reward at index $index is not a valid Item.");
                }
            }
        }
    }

    private function giveRewardsOnClose(Player $player, Inventory $inventory): void
    {
        $rewardSlots = $this->settings['reward-slots'] ?? [];
        $bonusRewardSlots = $this->settings['bonus-rewards-slots'] ?? [];
    
        $allRewards = array_merge($this->rewards, $this->bonusRewards);
        shuffle($allRewards);
    
        $givenItems = [];
        foreach (array_merge($rewardSlots, $bonusRewardSlots) as $slot) {
            $item = $inventory->getItem($slot);
            if (!$item->isNull()) {
                $player->getInventory()->addItem($item);
                $givenItems[] = $item; 
            }
        }
    
        if (isset($this->settings['broadcast']) && $this->settings['broadcast']['enable']) {
            $formattedItems = array_map(function ($item) {
                return C::colorize(
                    str_replace(
                        ['{AMOUNT}', '{ITEM}', '{PLAYER}'],
                        [$item->getCount(), $item->getName()],
                        $this->settings['broadcast']['message']
                    )
                );
            }, $givenItems);
    
            $broadcastMessage = C::colorize($this->settings['broadcast']['header']) . "\n" . implode("\n", $formattedItems);
    
            $onlinePlayers = Server::getInstance()->getOnlinePlayers();

            foreach ($onlinePlayers as $player) {
                $player->sendMessage(C::colorize(str_replace("{PLAYER}", $player->getName(), $broadcastMessage)));
            }
        }
    }

    
    /** TODO:
     * Make self::display static?
     */
    public static function resend(Player $player): void {
        self::$menu->send($player);
    }
}
