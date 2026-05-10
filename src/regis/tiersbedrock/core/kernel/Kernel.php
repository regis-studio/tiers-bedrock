<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\kernel;

use pocketmine\plugin\Plugin;
use pocketmine\utils\SingletonTrait;

final class Kernel
{
    use SingletonTrait;
    private Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function launch(): void
    {
        (new ServerInitializer())->initialize();
    }

    public function shutdown(): void
    {
        (new ServerTerminator())->terminate();
    }
}