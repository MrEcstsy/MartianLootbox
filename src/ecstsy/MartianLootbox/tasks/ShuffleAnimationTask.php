<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\tasks;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\inventory\Inventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use pocketmine\utils\TextFormat as C;
use ecstsy\MartianUtilities\utils\ItemUtils;
use ecstsy\MartianUtilities\utils\PlayerUtils;
use pocketmine\item\Item;

class ShuffleAnimationTask extends Task {

    private int $ticksElapsed = 0;
    private int $remainingTime;
    private array $settings;
    /** @var Item[] */
    private array $rewards;
    /** @var Item[] */
    private array $bonusRewards;
    private Player $player;
    private Inventory $inventory;

    /**
     * @param Player $player
     * @param Inventory $inventory
     * @param array $settings Animation settings from config (timer slots, reward slots, etc.)
     * @param array $rewardData Data from the config's "rewards" key.
     * @param array $bonusRewardData Data from the config's "bonus_rewards" key.
     * @param int $totalTime Total animation time in seconds.
     */
    public function __construct(Player $player, Inventory $inventory, array $settings, array $rewardData, array $bonusRewardData, int $totalTime) {
        $this->player = $player;
        $this->inventory = $inventory;
        $this->settings = $settings;
        $this->rewards = ItemUtils::setupItems($rewardData, $player);
        $this->bonusRewards = ItemUtils::setupItems($bonusRewardData, $player);
        $this->remainingTime = $totalTime;

        PlayerUtils::playSound($this->player, $this->settings['sound']['start'] ?? 'random.click');
        $this->updateTimerSlots();
    }

    public function onRun(): void {
        $this->ticksElapsed++;

        if (!in_array($this->player, $this->inventory->getViewers(), true)) {
            $this->finalizeAnimation();
            $this->getHandler()->cancel();
            return;
        }

        if ($this->ticksElapsed % 20 === 0) {
            $this->remainingTime--;
            $this->updateTimerSlots();


            if ($this->remainingTime <= 0) {
                $this->finalizeAnimation();
                $this->getHandler()->cancel();
                return;
            }
        }

        if ($this->remainingTime > 1 && $this->ticksElapsed % 3 === 0) {
            $this->shuffleRewards();
            PlayerUtils::playSound($this->player, $this->settings['sound']['start'] ?? 'random.click');
        }
    }

    /**
     * Updates the timer slots in the inventory.
     */
    private function updateTimerSlots(): void {
        foreach ($this->settings['timer-slots'] as $slot) {
            $glassPane = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem();
            $displayTime = max(1, $this->remainingTime);
            $glassPane->setCount($displayTime);
            $glassPane->setCustomName(C::colorize("&r&7" . $displayTime));
            $this->inventory->setItem($slot, $glassPane);
        }
    }

    /**
     * Shuffle rewards (main and bonus) and update inventory.
     */
    private function shuffleRewards(): void {
        $this->setRandomRewards($this->settings['reward-slots'], $this->rewards);
        $this->setRandomRewards($this->settings['bonus-rewards-slots'], $this->bonusRewards);
    }
    
    private function setRandomRewards(array $slots, array $rewards): void {
        if (empty($rewards)) {
            return;
        }
    
        foreach ($slots as $slot) {
            $randomReward = $rewards[array_rand($rewards)];
    
            if ($randomReward instanceof Item) {
                $this->inventory->setItem($slot, $randomReward);
            }
        }
    }    

    /**
     * Finalize the animation and play the prize sound.
     */
    private function finalizeAnimation(): void {
        PlayerUtils::playSound($this->player, $this->settings['sound']['prize'] ?? 'player.levelup');
    }
}
