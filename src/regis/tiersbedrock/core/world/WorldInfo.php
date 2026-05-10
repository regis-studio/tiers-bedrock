<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world;

use regis\tiersbedrock\core\server\Karnel;
use regis\tiersbedrock\core\task\ClosureManager;
use regis\tiersbedrock\core\task\tasks\world\CopyWorldAsync;
use regis\tiersbedrock\core\task\tasks\world\DeleteWorldAsync;
use pocketmine\world\World;
use pocketmine\Server;

abstract class WorldInfo
{
    /** @var array<int, World> */
    protected array $worlds;

    /** @var int[] */
    protected array $triggerIds;
    protected string $name;
    protected string $displayName;
    protected string $folderName;
    protected string $canBiomeChange;

    /**
     * @param array<int, World> $worlds
     * @param string $name
     * @param string $displayName
     * @param int[] $triggerIds
     */
    public function __construct(array $worlds, string $name, string $displayName, string $folderName, array $triggerIds, string $canBiomeChange)
    {
        $this->worlds = $worlds;
        $this->name = $name;
        $this->displayName = $displayName;
        $this->triggerIds = $triggerIds;
        $this->folderName = $folderName;
        $this->canBiomeChange = $canBiomeChange;
    }

    /** @return World[] */
    public function getWorlds(): array
    {
        return $this->worlds;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /** @return int[] */
    public function getTriggerIds(): array
    {
        return $this->triggerIds;
    }

    public function canBiomeChange(): string
    {
        return $this->canBiomeChange;
    }

    public function generateWorld(callable $callback): void
    {
        $this->copyWorld($this->name, function (World $world) use ($callback): void {
            $this->worlds[$world->getId()] = $world;
            WorldInfoManager::getInstance()->generateWorld($this, $world);
            $callback($world);
        });
    }

    public function getWorldById(int $worldId): ?World
    {
        return $this->worlds[$worldId] ?? null;
    }

    public function removeWorld(int $worldId): void
    {
        $world = $this->getWorldById($worldId);
        if ($world === null) {
            return;
        }
        $worldName = $world->getFolderName();
        $path = Server::getInstance()->getDataPath() . 'worlds' . DIRECTORY_SEPARATOR;
        Server::getInstance()->getAsyncPool()->submitTask(new DeleteWorldAsync($worldName, $path));
        unset($this->worlds[$worldId]);
    }

    private function copyWorld(string $worldName, callable $callback): void
    {
        $newWorldName = $worldName . uniqid();
        $sourcePath = Karnel::getInstance()->getPlugin()->getResourceFolder() . 'storage' . DIRECTORY_SEPARATOR . 'worlds' . DIRECTORY_SEPARATOR . $this->folderName;
        $path = Server::getInstance()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR;
        $callbackId = ClosureManager::getInstance()->register($callback);
        Server::getInstance()->getAsyncPool()->submitTask(new CopyWorldAsync($worldName, $sourcePath, $newWorldName, $path, $callbackId));
    }
}
