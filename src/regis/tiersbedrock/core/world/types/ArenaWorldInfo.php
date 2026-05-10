<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\types;

use regis\tiersbedrock\core\world\WorldInfo;
use pocketmine\math\Vector3;

final class ArenaWorldInfo extends WorldInfo
{
    /** @var Vector3[] */
    private array $spawns;

    /**
     * @param string $name
     * @param string $displayName
     * @param Vector3[] $spawns
     * @param int[] $triggerIds
     * @param string $canBiomeChange
     */
    public function __construct(string $name, string $displayName, array $spawns, array $triggerIds, string $canBiomeChange)
    {
        $this->spawns = $spawns;
        parent::__construct([], $name, $displayName, 'arena', $triggerIds, $canBiomeChange);
    }

    /** @return Vector3[]  */
    public function getAllSpawns(): array
    {
        return $this->spawns;
    }
}