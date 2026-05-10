<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\trigger;

use regis\tiersbedrock\core\session\Session;
use regis\tiersbedrock\core\utils\ErrorReporter;
use pocketmine\math\AxisAlignedBB;
use Throwable;

abstract class Trigger
{
    public function __construct(
        protected string $name,
        protected int $interval,
        protected AxisAlignedBB $area,
        protected string $type,
        public readonly int $triggerId
    ) {
    }

    public function getTriggerId(): int
    {
        return $this->triggerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function getArea(): AxisAlignedBB
    {
        return $this->area;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function launch(Session $session): void
    {
        try {
            $this->onRun($session);
        } catch (Throwable $e) {
            ErrorReporter::getInstance()->onError($e, $session->getPlayer());
        }
    }

    abstract protected function onRun(Session $session): void;
}