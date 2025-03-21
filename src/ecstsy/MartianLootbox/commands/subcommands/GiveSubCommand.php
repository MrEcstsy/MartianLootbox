<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use ecstsy\MartianLootbox\commands\arguments\LootboxIdentifierArgument;
use ecstsy\MartianLootbox\commands\arguments\PlayerPrefixArgument;
use ecstsy\MartianLootbox\Loader;
use ecstsy\MartianLootbox\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as C;

final class GiveSubCommand extends BaseSubCommand {

    public function prepare(): void {
        $this->setPermission($this->getPermission());

        $this->registerArgument(0, new LootboxIdentifierArgument("lootbox", false));
        $this->registerArgument(1, new PlayerPrefixArgument("player", false));
        $this->registerArgument(2, new IntegerArgument("amount", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        $lootboxId = $args["lootbox"] ?? null;
        $target = $args["player"] ?? null;
        $amount = isset($args["amount"]) ? $args["amount"] : 1;
        $language = Loader::getLanguageManager();

        if($lootboxId === null) {
            $sender->sendMessage(C::colorize($language->getNested("error.specify-id")));
            return;
        }

        if($target === null || !($target instanceof Player)) {
            $sender->sendMessage(C::colorize($language->getNested("error.invalid-player")));
            return;
        }
        
        $item = Utils::createLootboxItem($lootboxId, (int)$amount);
        
        if($item === null) {
            $sender->sendMessage(C::colorize($language->getNested("error.invalid-lootbox")));
            return;
        }
        
        if(!$target->getInventory()->canAddItem($item)) {
            $sender->sendMessage(C::colorize($language->getNested("error.inventory-full")));
            return;
        }
        
        $target->getInventory()->addItem($item);
        $sender->sendMessage(C::colorize(str_replace(["{AMOUNT}", "{LOOTBOX}", "{PLAYER}"], [$amount, $lootboxId, $target->getName()], $language->getNested("commands.give.success"))));
    }

    public function getPermission(): ?string
    {
        return "martianlootbox.command.give";
    }
}