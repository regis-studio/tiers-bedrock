<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\types;

use regis\tiersbedrock\core\world\WorldInfo;
use pocketmine\math\Vector3;

final class DuelWorldInfo extends WorldInfo
{
    private Vector3 $pos1;
    private Vector3 $pos2;

    /**
     * @param string $name
     * @param string $displayName
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param int[] $triggerIds
     * @param string $canBiomeChange
     */
    public function __construct(string $name, string $displayName, Vector3 $pos1, Vector3 $pos2, array $triggerIds, string $canBiomeChange)
    {
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
        parent::__construct([], $name, $displayName, 'duel', $triggerIds, $canBiomeChange);
    }

    public function getPos1(): Vector3
    {
        return $this->pos1;
    }

    public function getPos2(): Vector3
    {
        return $this->pos2;
    }
}