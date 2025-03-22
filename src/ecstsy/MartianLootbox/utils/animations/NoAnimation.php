<?php

declare(strict_types=1);

namespace ecstsy\MartianLootbox\utils\animations;

use ecstsy\MartianUtilities\utils\ItemUtils;
use ecstsy\MartianUtilities\utils\PlayerUtils;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

final class NoAnimation extends BaseAnimation {

    public function execute(): void
    {
        $rewards = $this->config['rewards'] ?? [];
        $bonusRewards = $this->config['bonus-rewards'] ?? [];
        
        $rewardCount = $this->config['animation']['settings']['reward-count'] ?? 3;
        $bonusRewardCount = $this->config['animation']['settings']['bonus-reward-count'] ?? 1;

        shuffle($rewards);
        $selectedRewards = array_slice($rewards, 0, $rewardCount);

        shuffle($bonusRewards);
        $selectedBonusRewards = array_slice($bonusRewards, 0, $bonusRewardCount);

        $allSelectedRewards = array_merge($selectedRewards, $selectedBonusRewards);

        if (empty($allSelectedRewards)) {
            return;
        }
    
        $items = ItemUtils::setupItems($allSelectedRewards, $this->player);
    
        foreach ($items as $item) {
            $this->player->getInventory()->addItem($item);
        }
        
        if (isset($this->config['message'])) {
            $message = $this->formatMessage($this->config['message'], $items);
            $this->player->sendMessage(C::colorize(str_replace(["{AMOUNT}", "{ITEM}", "{PLAYER}"], [$item->getCount(), $item->getName(), $this->player->getName()], $message)));
        }
    
        if (isset($this->config['animation']['settings']['sound']['claim'])) {
            PlayerUtils::playSound($this->player, $this->config['animation']['settings']['sound']['claim']);
        }
    
        if (isset($this->config['animation']['settings']['broadcast']) && $this->config['animation']['settings']['broadcast']['enable']) {
            $formattedItems = array_map(function ($item) {
                return C::colorize(
                    str_replace(
                        ['{AMOUNT}', '{ITEM}', '{PLAYER}'],
                        [$item->getCount(), $item->getName()],
                        $this->config['animation']['settings']['broadcast']['message']
                    )
                );
            }, $items);
    
            $broadcastMessage = C::colorize($this->config['animation']['settings']['broadcast']['header']) . "\n" . implode("\n", $formattedItems);
    
            $onlinePlayers = Server::getInstance()->getOnlinePlayers();
    
            foreach ($onlinePlayers as $player) {
                $player->sendMessage(C::colorize(str_replace("{PLAYER}", $player->getName(), $broadcastMessage)));
            }
        }
    }    

    private function formatMessage(string $message, array $items): string
    {
        $itemNames = [];
        foreach ($items as $item) {
            $itemNames[] = "{$item->getCount()}x " . $item->getName();
        }

        return str_replace([
            "{PLAYER}",
            "{ITEM}",
            "{AMOUNT}"
        ], [
            $this->player->getName(),
            implode("\n ", $itemNames),
            array_sum(array_map(fn(Item $item) => $item->getCount(), $items))
        ], $message);
    }
}
