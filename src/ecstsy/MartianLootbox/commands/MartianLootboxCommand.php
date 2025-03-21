<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\commands;

use CortexPE\Commando\BaseCommand;
use ecstsy\MartianLootbox\commands\subcommands\GiveSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;

final class MartianLootboxCommand extends BaseCommand {

    public function prepare(): void {
        $this->setPermission($this->getPermission());
        
        $this->registerSubCommand(new GiveSubCommand($this->plugin, "give", "Give a lootbox to a player"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $messages = [
            "&r&8" . str_repeat("-", 40),
            "&r&5&l✦ Martian Lootbox &r&d(v1.0.0) ✦",
            "&r",
            "&r&d☄ &b/ml give <id> <player> [amount] &8- &7Give a lootbox to a player",
            "&r&d☄ &b/ml list &8- &7List all available lootboxes",
            "&r&d☄ &b/ml info &8- &7Display plugin information.",
            "&r&8" . str_repeat("-", 40),
        ];

        foreach ($messages as $message) {
            $sender->sendMessage(C::colorize($message));
        }
    }

    public function getPermission(): string
    {
        return 'martianlootbox.command';
    }
}