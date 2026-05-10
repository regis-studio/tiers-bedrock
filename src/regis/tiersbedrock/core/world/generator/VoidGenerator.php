<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\generator;

use pocketmine\block\BlockTypeIds;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

final class VoidGenerator extends Generator
{

    public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
        $chunk = $world->getChunk($chunkX, $chunkZ);
        if ($chunk === null) {
            return;
        }

        if ($chunkX === 16 && $chunkZ === 16) {
            $chunk->setBlockStateId(0, 64, 0, BlockTypeIds::GRASS >> 4);
        }
    }

    public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void
    {
    }
}