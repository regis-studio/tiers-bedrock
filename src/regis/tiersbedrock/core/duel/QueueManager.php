<?php

namespace regis\tiersbedrock\core\duel;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class QueueManager
{
    use SingletonTrait;
    /** @var array<string, array<string, Player>> */
    private array $queues = [];
    public function addQueue(Player $player, BaseMode $mode): void
    {
        $name = $mode->getName();
        if (isset($this->queues[$name][$player->getXuid()])) {
            return;
        }
        $this->queues[$name][$player->getXuid()] = $player;
        if ($this->getQueuePlayerCount($mode) >= 2) {
            $players = array_values($this->queues[$name]);
            $player1 = $players[0];
            $player2 = $players[1];
            unset($this->queues[$name][$player1->getXuid()]);
            unset($this->queues[$name][$player2->getXuid()]);
            if (empty($this->queues[$name])) {
                unset($this->queues[$name]);
            }
            $matchClass = $mode->getMatchClass();
            $match = new $matchClass($player1, $player2, $mode);
            MatchManager::getInstance()->addMatch($match);
            $match->start();
        }
    }

    public function removeQueue(Player $player, BaseMode $mode): void
    {
        $name = $mode->getName();
        if (!isset($this->queues[$name][$player->getXuid()])) {
            return;
        }

        unset($this->queues[$name][$player->getXuid()]);

        if (empty($this->queues[$name])) {
            unset($this->queues[$name]);
        }
    }

    public function getQueue(string $queuename): ?array
    {
        if (isset($this->queues[$queuename])) {
            return $this->queues[$queuename];
        }
        return null;
    }

    public function getQueuePlayerCount(BaseMode $mode): int
    {
        $name = $mode->getName();

        if (!isset($this->queues[$name])) {
            return 0;
        }
        return count($this->queues[$name]);
    }
}
