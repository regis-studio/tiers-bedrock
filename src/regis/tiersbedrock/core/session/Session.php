<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\session;

use pocketmine\player\Player;

final class Session
{
    private Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}