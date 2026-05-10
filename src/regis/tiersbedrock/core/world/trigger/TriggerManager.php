<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\world\trigger;

use Generator;
use regis\tiersbedrock\core\world\trigger\triggers\KnockbackTrigger;
use regis\tiersbedrock\core\world\trigger\triggers\MessageTrigger;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\utils\SingletonTrait;

final class TriggerManager
{
    use SingletonTrait;

    /** @var Trigger[] */
    private array $triggers = [];

    /** @var array<string, Trigger[]> */
    private array $triggersByChunk = [];
    private int $triggerId = 0;

    /**
     * @param string $name actionName
     * @param array<string, mixed> $data JSONの中身
     */
    public function createAndRegister(string $name, array $data): ?Trigger
    {
        if (!isset($data['type'], $data['interval'], $data['position'])) {
            return null;
        }

        $posData = $data['position'];

        $pos1 = $this->parseVector3($posData['pos1'] ?? null);
        $pos2 = $this->parseVector3($posData['pos2'] ?? null);

        if ($pos1 === null || $pos2 === null) {
            return null;
        }

        /*
          AABBはmin/maxが正しい順序である必要があるため正規化
          基本はmin,max共に座標気にすることなくそのままブロック座標コピペでok(正規化してmaxの方は+1するので)
          ただしそれに加えて、値が少数の場合は+1せずにそのまま使用します
        */
        $getCorrectMax = function (float $v1, float $v2): float {
            $max = max($v1, $v2);
            if ($v1 == (int) $v1 && $v2 == (int) $v2) {
                return $max + 1.0;
            }
            return $max;
        };

        $area = new AxisAlignedBB(
            min($pos1->x, $pos2->x),
            min($pos1->y, $pos2->y),
            min($pos1->z, $pos2->z),
            $getCorrectMax($pos1->x, $pos2->x),
            $getCorrectMax($pos1->y, $pos2->y),
            $getCorrectMax($pos1->z, $pos2->z)
        );

        /** @var array<string, mixed> */
        $actionInfo = $data['actionInfo'] ?? [];
        $triggerId = $this->nextTriggerId();

        $trigger = match ($data['type']) {
            'knockback' => $this->createKnockbackTrigger($name, $data, $area, $actionInfo, $triggerId),
            'message' => $this->createMessageTrigger($name, $data, $area, $actionInfo, $triggerId),
            default => null,
        };

        if ($trigger !== null) {
            $this->register($trigger);
        }

        return $trigger;
    }

    /**
     * @param string $name
     * @param array<string, mixed> $data
     * @param AxisAlignedBB $area
     * @param array<string, mixed>|string $actionInfo
     * @param int $triggerId
     * @return KnockbackTrigger
     */
    private function createKnockbackTrigger(string $name, array $data, AxisAlignedBB $area, array|string $actionInfo, int $triggerId): KnockbackTrigger
    {
        $direction = $this->parseVector3($actionInfo);
        if ($direction === null) {
            $direction = new Vector3(0, 0, 0);
        }

        return new KnockbackTrigger(
            $name,
            (int) $data['interval'],
            $area,
            $direction,
            $triggerId
        );
    }

    /**
     * @param string $name
     * @param array<string, mixed> $data
     * @param AxisAlignedBB $area
     * @param array<string, mixed>|string $actionInfo
     * @param int $triggerId
     * @return MessageTrigger
     */
    private function createMessageTrigger(string $name, array $data, AxisAlignedBB $area, array|string $actionInfo, int $triggerId): MessageTrigger
    {
        $direction = $this->parseVector3($actionInfo);
        if ($direction === null) {
            $direction = new Vector3(0, 0, 0);
        }

        return new MessageTrigger(
            $name,
            (int) $data['interval'],
            $area,
            (string) ($actionInfo['message'] ?? ""),
            $triggerId
        );
    }

    /**
     * @param array<string, int>|string $data
     * @return Vector3|null
     */
    private function parseVector3(array|string $data): ?Vector3
    {
        if (is_string($data)) {
            $parts = explode(':', $data);
            if (count($parts) < 3)
                return null;
            return new Vector3((float) $parts[0], (float) $parts[1], (float) $parts[2]);
        } elseif (is_array($data)) {
            if (isset($data['x'], $data['y'], $data['z'])) {
                return new Vector3((float) $data['x'], (float) $data['y'], (float) $data['z']);
            }
            if (count($data) >= 3 && isset($data['x'], $data['y'], $data['z'])) {
                return new Vector3((float) $data['x'], (float) $data['y'], (float) $data['z']);
            }
        }
        return null;
    }


    public function register(Trigger $trigger): void
    {
        $triggerId = $trigger->getTriggerId();
        $this->triggers[$triggerId] = $trigger;
        $chunks = $this->getChunksInArea($trigger->getArea());
        foreach ($chunks as [$cx, $cy, $cz]) {
            $this->triggersByChunk[$cx . ':' . $cy . ':' . $cz][] = $trigger;
        }
    }

    public function getTrigger(int $triggerId): ?Trigger
    {
        return $this->triggers[$triggerId] ?? null;
    }

    /**
     * @param string $chunkId
     * @return Trigger[]
     */
    public function getTriggerByChunk(string $chunkId): array
    {
        return $this->triggersByChunk[$chunkId] ?? [];
    }

    /** @return Trigger[] */
    public function getAll(): array
    {
        return $this->triggers;
    }

    public function removeTrigger(int $triggerId): void
    {
        if (!isset($this->triggers[$triggerId])) {
            return;
        }

        $trigger = $this->triggers[$triggerId];
        $chunks = $this->getChunksInArea($trigger->getArea());
        foreach ($chunks as [$cx, $cy, $cz]) {
            $hash = "$cx:$cy:$cz";
            if (isset($this->triggersByChunk[$hash][$triggerId])) {
                unset($this->triggersByChunk[$hash][$triggerId]);
                if (empty($this->triggersByChunk[$hash])) {
                    unset($this->triggersByChunk[$hash]);
                }
            }
        }

        unset($this->triggers[$triggerId]);
    }

    public function nextTriggerId(): int
    {
        return $this->triggerId++;
    }


    private function getChunksInArea(
        AxisAlignedBB $aabb,
        int $shift = 4
    ): Generator {
        $minCX = (int) $aabb->minX >> $shift;
        $minCY = (int) $aabb->minY >> $shift;
        $minCZ = (int) $aabb->minZ >> $shift;

        $maxCX = (int) $aabb->maxX >> $shift;
        $maxCY = (int) $aabb->maxY >> $shift;
        $maxCZ = (int) $aabb->maxZ >> $shift;

        for ($x = $minCX; $x <= $maxCX; ++$x) {
            for ($y = $minCY; $y <= $maxCY; ++$y) {
                for ($z = $minCZ; $z <= $maxCZ; ++$z) {
                    yield [$x, $y, $z];
                }
            }
        }
    }
}