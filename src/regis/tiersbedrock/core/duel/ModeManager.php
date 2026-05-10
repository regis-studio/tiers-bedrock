<?php

namespace regis\tiersbedrock\core\duel;

use pocketmine\utils\SingletonTrait;
use regis\tiersbedrock\core\duel\BaseMode;

final class ModeManager
{
    use SingletonTrait;
    /** @var array<string, BaseMode> */
    private array $modes = [];

    public function addMode(BaseMode $mode): void
    {
        $name = $mode->getName();
        if (isset($this->modes[$name])) {
            return;
        }
        $this->modes[$name] = $mode;
    }

    public function getMode(string $name): ?BaseMode
    {
        if (!isset($this->modes[$name])) {
            return null;
        }
        return $this->modes[$name];
    }

    public function getModes(): array
    {
        return $this->modes;
    }
    public function removeMode(BaseMode $mode): void
    {
        $name = $mode->getName();
        if (!isset($this->modes[$name])) {
            return;
        }
        unset($this->modes[$name]);
    }

    public function load(): void 
    {
        //$this->addMode(new SkyWarsMode());
    }
}
