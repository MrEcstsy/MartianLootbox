<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\commands\arguments;

use CortexPE\Commando\args\BaseArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use ecstsy\MartianUtilities\utils\PlayerUtils;
use pocketmine\player\Player;

class PlayerPrefixArgument extends BaseArgument {

    public function getNetworkType(): int {
        return AvailableCommandsPacket::ARG_TYPE_STRING;
    }

    public function getTypeName(): string {
        return "player";
    }

    public function canParse(string $testString, CommandSender $sender): bool {
        return PlayerUtils::getPlayerByPrefix($testString) !== null;
    }

    /**
     * @throws \InvalidArgumentException 
     */
    public function parse(string $argument, CommandSender $sender): Player {
        $player = PlayerUtils::getPlayerByPrefix($argument);
        if($player === null){
            throw new \InvalidArgumentException("Invalid player: " . $argument);
        }
        return $player;
    }
}
