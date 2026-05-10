<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\trigger\triggers;

use regis\tiersbedrock\core\session\Session;
use regis\tiersbedrock\core\world\trigger\Trigger;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

final class KnockbackTrigger extends Trigger
{
    private Vector3 $motion;
    public function __construct(
        string $name,
        int $interval,
        AxisAlignedBB $area,
        Vector3 $motion,
        int $triggerId
    ) {
        $this->motion = $motion;
        parent::__construct($name, $interval, $area, "knockback", $triggerId);
    }

    public function getForce(): Vector3
    {
        return $this->motion;
    }

    protected function onRun(Session $session): void
    {
        $session->getPlayer()->setMotion($this->motion);
    }
}