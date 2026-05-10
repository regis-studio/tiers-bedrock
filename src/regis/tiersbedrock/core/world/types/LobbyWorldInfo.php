<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\types;

use regis\tiersbedrock\core\world\WorldInfo;
use pocketmine\math\Vector3;

final class LobbyWorldInfo extends WorldInfo
{
    private Vector3 $spawn;

    /**
     * @param string $name
     * @param string $displayName
     * @param Vector3 $spawn
     * @param int[] $triggerIds
     * @param string $canBiomeChange
     */
    public function __construct(string $name, string $displayName, Vector3 $spawn, array $triggerIds, string $canBiomeChange)
    {
        $this->spawn = $spawn;
        parent::__construct([], $name, $displayName, 'lobby', $triggerIds, $canBiomeChange);
    }

    public function getSpawn(): Vector3
    {
        return $this->spawn;
    }
}