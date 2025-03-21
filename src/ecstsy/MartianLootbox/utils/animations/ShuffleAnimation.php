<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils\animations;

use ecstsy\MartianLootbox\Loader;
use ecstsy\MartianLootbox\tasks\ShuffleAnimationTask;
use ecstsy\MartianLootbox\utils\screens\ShuffleScreen;
use pocketmine\utils\TextFormat as C;

final class ShuffleAnimation extends BaseAnimation {

    public function execute(): void
    {
        $settings = $this->config['animation']['settings'] ?? [];
        $rewards = $this->config['rewards'] ?? [];
        $bonusRewards = $this->config['bonus-rewards'] ?? [];

        $this->ensureRewardAlways($this->config);

        $screen = new ShuffleScreen($settings, $rewards, $bonusRewards);
        $screen->display($this->player);

        $task = new ShuffleAnimationTask($this->player, $screen->getInventory(), $settings, $rewards, $bonusRewards, $settings['time'] ?? 5);
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask($task, 1); // Use 1 for quicker timing
    }

    private function ensureRewardAlways(array &$config): void
    {
        if (empty($config['rewards']) && empty($config['bonus-rewards'])) {
            $config['rewards'] = [['item' => 'diamond']];
        } else {
            if (empty($config['rewards'][0]['item'])) {
                $config['rewards'][0]['item'] = 'diamond';
            }
        }
    }
}
