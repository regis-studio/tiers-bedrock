<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\event;

use pocketmine\event\Event;

class WorldDeleteEvent extends Event
{

    public function __construct(
        private string $worldName
    ) {
    }

    public function getWorldName(): string
    {
        return $this->worldName;
    }
}
