<?php

namespace regis\tiersbedrock\core\duel;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class MatchManager
{
    use SingletonTrait;
    /** @var array <string, DuelMatch> */
    private array $matches = [];

    public function addMatch(BaseMatch $match): void
    {
        $this->matches[$match->getPlayer1()->getXuid()] = $match;
        $this->matches[$match->getPlayer2()->getXuid()] = $match;
    }

    public function getMatch(Player $player): ?BaseMatch
    {
        return $this->matches[$player->getXuid()] ?? null;
    }

    public function removeMatch(BaseMatch $match): void
    {
        unset($this->matches[$match->getPlayer1()->getXuid()]);
        unset($this->matches[$match->getPlayer2()->getXuid()]);
    }
}
