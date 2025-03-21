<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils\animations;

use pocketmine\player\Player;

abstract class BaseAnimation {
    protected Player $player;
    protected array $config;

    public function __construct(Player $player, array $config) {
        $this->player = $player;
        $this->config = $config;
    }

    abstract public function execute(): void;
}
