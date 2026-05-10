<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world;

use regis\tiersbedrock\core\server\Karnel;
use regis\tiersbedrock\core\task\tasks\world\DeleteWorldTask;
use regis\tiersbedrock\core\world\chunk\ChunkLoad;
use regis\tiersbedrock\core\world\trigger\Trigger;
use regis\tiersbedrock\core\world\trigger\TriggerManager;
use regis\tiersbedrock\core\world\types\ArenaWorldInfo;
use regis\tiersbedrock\core\world\types\DuelWorldInfo;
use regis\tiersbedrock\core\world\types\LobbyWorldInfo;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use RuntimeException;

final class WorldInfoManager
{
    use SingletonTrait;

    /** @var WorldInfo[] */
    private array $worldInfos = [];

    /** @var array<int, WorldInfo> */
    private array $worlds = [];

    public function register(WorldInfo $worldInfo): void
    {
        $this->worldInfos[$worldInfo->getName()] = $worldInfo;
    }

    public function get(string $worldName): ?WorldInfo
    {
        return $this->worldInfos[$worldName] ?? null;
    }

    /**
     * @return WorldInfo[]
     */
    public function getAll(): array
    {
        return $this->worldInfos;
    }

    public function generateWorld(WorldInfo $worldInfo, World $world): void
    {
        $this->worlds[$world->getId()] = $worldInfo;
    }

    public function getWorldInfoByWorldId(int $worldId): ?WorldInfo
    {
        return $this->worlds[$worldId] ?? null;
    }

    public function load(): void
    {
        $plugin = Karnel::getInstance()->getPlugin();
        $path = Server::getInstance()->getDataPath() . 'worlds' . DIRECTORY_SEPARATOR;
        $defaultWorld = "login";
        foreach ($this->getDirectoryFolders($path) as $worldName) {
            if ($worldName === $defaultWorld) {
                continue;
            }
            $plugin->getScheduler()->scheduleTask(new DeleteWorldTask($worldName, $path));
        }
        ChunkLoad::init();
        $dir = $plugin->getResourceFolder() . 'storage' . DIRECTORY_SEPARATOR . 'worlds.json';

        if (!is_dir($dir)) {
            @mkdir($dir);
        }
        $config = new Config($dir, Config::JSON);

        try {
            $this->parseWorld($config->getAll());
        } catch (RuntimeException $exception) {
            $plugin->getLogger()->info('World parse error: ' . $exception->getMessage());
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return void
     */
    private function parseWorld(array $data): void
    {
        // ------------------ Lobby ------------------
        foreach ($data['lobby'] ?? [] as $worldName => $worldData) {
            if (!isset($worldData["spawnPoint"])) {
                continue;
            }

            $spawnLocation = $this->parseCoordinate((string) $worldData["spawnPoint"]);

            if ($spawnLocation === null) {
                continue;
            }

            $displayName = $worldData["displayName"] ?? $worldName;
            $triggers = $this->checkTriggers($worldData);
            $canBiomeChange = $worldData["biomeChange"] ?? "";

            $this->register(new LobbyWorldInfo($worldName, $displayName, $spawnLocation, $triggers, $canBiomeChange));
        }

        // ------------------ Arena ------------------
        foreach ($data['arena'] ?? [] as $worldName => $worldData) {
            $spawns = [];
            foreach ($worldData["spawns"] ?? [] as $spawnData) {
                $vec = $this->parseCoordinate((string) $spawnData);
                if ($vec !== null) {
                    $spawns[] = $vec;
                }
            }

            $displayName = $worldData["displayName"] ?? $worldName;
            $triggers = $this->checkTriggers($worldData);
            $canBiomeChange = $worldData["biomeChange"] ?? "";

            $this->register(new ArenaWorldInfo($worldName, $displayName, $spawns, $triggers, $canBiomeChange));
        }

        // ------------------ Duel ------------------
        foreach ($data['duel'] ?? [] as $worldName => $worldData) {
            $pos1Data = $worldData['first-position'] ?? $worldData['pos1'] ?? null;
            $pos2Data = $worldData['second-position'] ?? $worldData['pos2'] ?? null;

            if ($pos1Data === null || $pos2Data === null) {
                continue;
            }

            $pos1 = $this->parseCoordinate((string) $pos1Data);
            $pos2 = $this->parseCoordinate((string) $pos2Data);

            if ($pos1 === null || $pos2 === null) {
                continue;
            }

            $displayName = $worldData["displayName"] ?? $worldName;
            $triggers = $this->checkTriggers($worldData);
            $canBiomeChange = $worldData["biomeChange"] ?? "";

            $this->register(new DuelWorldInfo($worldName, $displayName, $pos1, $pos2, $triggers, $canBiomeChange));
        }
    }

    /**
     * @param string $data
     * @return Vector3|null
     */
    private function parseCoordinate(string $data): ?Vector3
    {
        $parts = explode(':', str_replace(' ', '', $data));
        $count = count($parts);

        if ($count !== 3 && $count !== 5) {
            return null;
        }

        $adjust = fn(string $val) => (strpos($val, '.') === false) ? (float) $val + 0.5 : (float) $val;

        $x = $adjust($parts[0]);
        $y = (float) $parts[1];
        $z = $adjust($parts[2]);

        if ($count === 5) {
            return new Location($x, $y, $z, null, (float) $parts[3], (float) $parts[4]);
        }

        return new Vector3($x, $y, $z);
    }


    /**
     * @param array<string, mixed> $worldData
     * @return int[]
     */
    private function checkTriggers(array $worldData): array
    {
        $triggerIds = [];
        if (isset($worldData["triggers"])) {
            foreach ($worldData["triggers"] as $triggerName => $triggerData) {
                $trigger = TriggerManager::getInstance()->createAndRegister($triggerName, $triggerData);
                if ($trigger instanceof Trigger) {
                    $triggerIds[] = $trigger->getTriggerId();
                }
            }
        }
        return $triggerIds;
    }

    /**
     * @param string $path
     * @return string[]
     */
    private function getDirectoryFolders(string $path): array
    {
        $folderNames = [];
        if (!is_dir($path)) {
            return [];
        }

        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $folderNames[] = $fileInfo->getFilename();
            }
        }

        return $folderNames;
    }
}