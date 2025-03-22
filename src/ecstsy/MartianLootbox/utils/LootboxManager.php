<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils;

use ecstsy\MartianLootbox\Loader;
use ecstsy\MartianLootbox\utils\animations\NoAnimation;
use ecstsy\MartianLootbox\utils\animations\ShuffleAnimation;
use ecstsy\MartianLootbox\utils\screens\LootboxPreviewScreen;
use ecstsy\MartianUtilities\utils\GeneralUtils;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as C;

final class LootboxManager {

    use SingletonTrait;

    private string $lootboxPath;
    private string $dataFolder;

    public function __construct(string $dataFolder)
    {
        self::setInstance($this);

        $this->dataFolder = $dataFolder;
        $this->lootboxPath = "lootboxes/";
    }

    public function getLootboxConfiguration(string $id): ?array
    {
        $filePath = $this->lootboxPath . $id . ".yml";

        if (!file_exists($this->dataFolder . $filePath)) {
            return null;
        }
        
        $config = GeneralUtils::getConfiguration(Loader::getInstance(), $filePath);
        if ($config === null) {
            return null;
        }

        return $config->getAll();
    }

    public function openLootbox(Player $player, string $id): void
    {
        $config = $this->getLootboxConfiguration($id);
        $language = Loader::getLanguageManager();
    
        if ($config === null) {
            $player->sendMessage(C::colorize(str_replace("{IDENTIFIER}", $id, $language->getNested("error.lootbox-not-found"))));
            return;
        }
    
        $animationClass = match ($config['animation']['type'] ?? 0) {
            1 => ShuffleAnimation::class,
            default => NoAnimation::class,
        };
    
        $animation = new $animationClass($player, $config);
        $animation->execute();  
    }
    
    public function previewLootbox(Player $player, string $id): void
    {
        $config = $this->getLootboxConfiguration($id);

        if ($config === null) {
            $player->sendMessage(C::colorize(str_replace("{IDENTIFIER}", $id, Loader::getLanguageManager()->getNested("error.lootbox-not-found"))));
            return;
        }
        
        $preview = new LootboxPreviewScreen($config['animation']['settings'], $config['rewards'], $config['bonus-rewards'], $config['animation']['settings']['reward-preview'] ?? null);
        $preview->display($player);
    }
}
