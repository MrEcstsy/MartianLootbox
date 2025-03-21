<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\commands\arguments;

use CortexPE\Commando\args\BaseArgument;
use ecstsy\MartianLootbox\Loader;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use ecstsy\MartianLootbox\utils\LootboxManager;
use ecstsy\MartianUtilities\utils\GeneralUtils;

class LootboxIdentifierArgument extends BaseArgument {

    public function getNetworkType(): int {
        return AvailableCommandsPacket::ARG_TYPE_STRING;
    }

    public function getTypeName(): string {
        return "lootbox-id";
    }

    /**
     * Check if the given argument is a valid lootbox id.
     */
    public function canParse(string $testString, CommandSender $sender): bool {
        $filePath = GeneralUtils::getConfiguration(Loader::getInstance(), "lootboxes/$testString.yml");
    
        if ($filePath === null) {
            return false;
        }
        
        return file_exists($filePath->getPath());
    }
    

    /**
     * Parse the argument and return the lootbox id.
     *
     * @throws \InvalidArgumentException if the lootbox id is invalid.
     */
    public function parse(string $argument, CommandSender $sender): string {
        if(!$this->canParse($argument, $sender)){
            throw new \InvalidArgumentException("Invalid lootbox ID: " . $argument);
        }

        return $argument;
    }
}
