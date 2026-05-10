<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\chunk;

use regis\tiersbedrock\core\world\WorldInfoManager;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\SubChunk;
use pocketmine\world\format\PalettedBlockArray;

class ChunkLoad
{
    use SingletonTrait;

    private static int $defaultBiomeId = BiomeIds::ICE_PLAINS_SPIKES;

    /** @phpstan-ignore-next-line @var array<int, PalettedBlockArray>  */
    private static array $biomeArrays = [];

    public static function init(): void
    {
        $allBiomeIds = StringToBiomeIdParser::getInstance()->getAllBiomeIds();
        foreach ($allBiomeIds as $id) {
            // @phpstan-ignore-next-line
            self::$biomeArrays[$id] = new PalettedBlockArray($id);
        }
    }

    public static function resolveBiomeId(string $idString): int
    {
        if ($idString === "") {
            return self::$defaultBiomeId;
        }

        $parsed = StringToBiomeIdParser::getInstance()->parse($idString);
        return $parsed ?? self::$defaultBiomeId;
    }

    public static function onChunkLoad(ChunkLoadEvent $event): void
    {
        $worldId = $event->getWorld()->getId();
        $worldInfo = WorldInfoManager::getInstance()->getWorldInfoByWorldId($worldId);

        if ($worldInfo === null) {
            return;
        }

        $idString = $worldInfo->canBiomeChange();
        $targetBiomeId = self::resolveBiomeId($idString);

        $targetArray = self::$biomeArrays[$targetBiomeId];
        $targetPalette = [$targetBiomeId];

        $chunk = $event->getChunk();

        foreach ($chunk->getSubChunks() as $y => $subChunk) {
            // @phpstan-ignore class.notFound
            if ($subChunk->getBiomeArray()->getPalette() === $targetPalette) {
                continue;
            }

            $chunk->setSubChunk(
                $y,
                new SubChunk(
                    $subChunk->getEmptyBlockId(),
                    $subChunk->getBlockLayers(),
                    $targetArray,
                    $subChunk->getBlockSkyLightArray(),
                    $subChunk->getBlockLightArray()
                )
            );
        }
    }
}