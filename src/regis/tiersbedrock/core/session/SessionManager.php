<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\session;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class SessionManager
{
    use SingletonTrait;
    /** @var Session[] */
    private array $sessions = [];

    public function createSession(Player $player): Session
    {
        $session = new Session($player);
        $this->sessions[$player->getXuid()] = $session;
        return $session;
    }

    public function getSession(Player $player): ?Session
    {
        return $this->sessions[$player->getXuid()] ?? null;
    }

    public function removeSession(Player $player): void
    {
        unset($this->sessions[$player->getXuid()]);
    }

    public function hasSession(Player $player): bool
    {
        return isset($this->sessions[$player->getXuid()]);
    }

    /**
     * Get all sessions
     * @return Session[]
     */
    public function getSessions(): array
    {
        return $this->sessions;
    }
}