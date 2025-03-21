<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils\animations;

use pocketmine\utils\TextFormat as C;

final class NoAnimation extends BaseAnimation {

    public function execute(): void
    {
        $rewards = $this->config['rewards'] ?? [];

        foreach ($rewards as $rewardData) {
            // TODO: HANDLE
        }

        if (isset($this->config['message'])) {
            $this->player->sendMessage(C::colorize($this->config['message']));
        }
    }
}