<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;

final class ListSubCommand extends BaseSubCommand {

    private static ?array $cachedLootboxes = null;
    private const MAX_LOOTBOXES_PER_PAGE = 10; 

    public function prepare(): void {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (self::$cachedLootboxes === null) {
            $lootboxesDirectory = $this->plugin->getDataFolder() . "lootboxes/";

            if (!is_dir($lootboxesDirectory)) {
                $sender->sendMessage(C::colorize("&r&4Error: &r&cLootboxes directory not found."));
                return;
            }

            $files = glob($lootboxesDirectory . "*.yml");

            if (empty($files)) {
                $sender->sendMessage(C::colorize("&r&4Error: &r&cNo lootboxes found."));
                return;
            }

            self::$cachedLootboxes = array_map(fn($file) => basename($file, ".yml"), $files);
        }

        $totalLootboxes = count(self::$cachedLootboxes);
        $page = $args[0] ?? 1; 
        $page = max(1, (int)$page); 

        $startIndex = ($page - 1) * self::MAX_LOOTBOXES_PER_PAGE;
        $endIndex = min($startIndex + self::MAX_LOOTBOXES_PER_PAGE, $totalLootboxes);

        if ($startIndex >= $totalLootboxes) {
            $sender->sendMessage(C::colorize("&r&4Error: &r&cInvalid page number."));
            return;
        }

        $message = C::GREEN . "&r&8" . str_repeat("-", 40) . "\n";
        $message .= C::colorize("&r&d&l✦ &5Available Lootboxes &8(&b$page&8/&b" . ceil($totalLootboxes / self::MAX_LOOTBOXES_PER_PAGE) . "&8) &d✦\n");
        $message .= C::GREEN . "&r\n";

        for ($i = $startIndex; $i < $endIndex; $i++) {
            $lootbox = self::$cachedLootboxes[$i];
            $message .= C::AQUA . "&r&d☄ &b" . $lootbox . "\n";
        }

        $nextPage = ($page * self::MAX_LOOTBOXES_PER_PAGE < $totalLootboxes) ? $page + 1 : $page;
        $prevPage = $page > 1 ? $page - 1 : $page;
        $message .= C::GREEN . "&r&8" . str_repeat("-", 40) . "\n";
        $message .= C::colorize("&r&7&o     ((/ml list $prevPage for previous page, /ml list $nextPage for next page.))\n");

        $sender->sendMessage(C::colorize($message));
    }

    public function getPermission(): ?string
    {
        return "martianlootbox.command.list";
    }
}
