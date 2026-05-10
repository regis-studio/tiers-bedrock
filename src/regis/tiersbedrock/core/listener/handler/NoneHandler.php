<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\listener\handler;

use regis\tiersbedrock\core\listener\dispatch\DispatchInterface;
use regis\tiersbedrock\core\listener\dispatch\DispatchTrait;
use pocketmine\utils\SingletonTrait;
final class NoneHandler implements DispatchInterface
{
    use SingletonTrait;
    use DispatchTrait;
}