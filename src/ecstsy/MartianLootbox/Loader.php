<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox;

use ecstsy\MartianLootbox\commands\MartianLootboxCommand;
use ecstsy\MartianLootbox\listeners\EventListener;
use ecstsy\MartianLootbox\utils\LootboxManager;
use ecstsy\MartianLootbox\utils\Utils;
use ecstsy\MartianUtilities\managers\LanguageManager;
use ecstsy\MartianUtilities\utils\GeneralUtils;
use JackMD\ConfigUpdater\ConfigUpdater;
use libCustomPack\libCustomPack;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\resourcepacks\ZippedResourcePack;
use pocketmine\utils\SingletonTrait;
use Symfony\Component\Filesystem\Path;

final class Loader extends PluginBase {

    use SingletonTrait;

    private static ?ZippedResourcePack $pack;

    public const TYPE_DYNAMIC_PREFIX = "martianlootbox:customsizedinvmenu_";

    public static LanguageManager $languageManager;

    public static LootboxManager $lootboxManager;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $config = GeneralUtils::getConfiguration($this, "config.yml");

        ConfigUpdater::checkUpdate($this, $config, "v", 1);

        $directories = ["locale", "lootboxes"];

        foreach ($directories as $directory) {
            $this->saveAllFilesInDirectory($directory);
        }

        $listeners = [
            new EventListener(),
        ];

        foreach ($listeners as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        libCustomPack::registerResourcePack(self::$pack = libCustomPack::generatePackFromResources($this));
        Utils::initCustomSizedMenu();

        $this->getServer()->getCommandMap()->registerAll("MartianLootbox", [
            new MartianLootboxCommand($this, "martianlootbox", "Manage Martian Lootboxes", ["ml", "mlb"]),
        ]);

        self::$languageManager = new LanguageManager($this, $config->getNested("settings.language"));
        self::$lootboxManager = new LootboxManager($this->getDataFolder());
    }

    protected function onDisable(): void
    {
        libCustomPack::unregisterResourcePack(self::$pack);
        $this->getLogger()->info("Resource pack unloaded");
        unlink(Path::join($this->getDataFolder(), self::$pack->getPackName() . ".mcpack"));
    }

    public static function getLanguageManager(): LanguageManager {
        return self::$languageManager;
    }

    public static function getLootboxManager(): LootboxManager {
        return self::$lootboxManager;
    }

    private function saveAllFilesInDirectory(string $directory): void {
        $resourcePath = $this->getFile() . "resources/$directory/";
        if (!is_dir($resourcePath)) {
            $this->getLogger()->warning("Directory $directory does not exist.");
            return;
        }

        $files = scandir($resourcePath);
        if ($files === false) {
            $this->getLogger()->warning("Failed to read directory $directory.");
            return;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $this->saveResource("$directory/$file");
        }
    }
}