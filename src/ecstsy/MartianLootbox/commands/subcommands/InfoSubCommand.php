<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use ecstsy\MartianLootbox\Loader;
use ecstsy\MartianUtilities\utils\GeneralUtils;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;

final class InfoSubCommand extends BaseSubCommand {

    public function prepare(): void {
        $this->setPermission($this->getPermission());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $config = GeneralUtils::getConfiguration(Loader::getInstance(), "config.yml");
        $messages = [
            "&r&d&l✦ &5MartianLootbox &d✦",
            "&r&d☄ &bLanguage: &7" . $config->getNested("settings.language"),
            "&r&d☄ &bVersion: &7" . $this->plugin->getDescription()->getVersion(),
            "&r&d☄ &bAuthor: &7" . $this->plugin->getDescription()->getAuthors()[0],
        ];

        foreach ($messages as $message) {
            $sender->sendMessage(C::colorize($message));
        }
    }

    public function getPermission(): ?string
    {
        return "martianlootbox.command.info";
    }
}
