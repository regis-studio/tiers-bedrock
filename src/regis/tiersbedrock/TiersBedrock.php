<?php

declare(strict_types=1);

namespace regis\tiersbedrock;

use pocketmine\plugin\PluginBase;
use regis\tiersbedrock\core\kernel\Kernel;

final class TiersBedrock extends PluginBase
{
    private Kernel $kernel;
    protected function onEnable(): void
    {
        $this->kernel = new Kernel($this);
        $this->kernel->launch();
    }

    protected function onDisable(): void
    {
        if (isset($this->kernel)) {
            $this->kernel->shutdown();
        }
    }
}