<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\event;

use pocketmine\event\Event;
use pocketmine\world\World;

class WorldGenerateEvent extends Event
{
    private World $world;

    public function __construct(World $world)
    {
        $this->world = $world;
    }

    public function getWorld(): World
    {
        return $this->world;
    }
}
