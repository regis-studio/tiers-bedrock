<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\command\core;

use regis\tiersbedrock\core\kernel\Kernel;
use regis\tiersbedrock\core\session\Session;
use pocketmine\utils\SingletonTrait;

final class PermissionUtils
{
    use SingletonTrait;

    /** @var array<string, array<string, boolean>> */
    private array $permissions = [];

    /** @var string[] */
    private array $knownPermissions = [];

    /** @param string[] $permissions */
    public function createPermission(array $permissions): void
    {
        foreach ($permissions as $name) {
            $this->knownPermissions[] = $name;
        }
    }

    public function resetPermissions(): void
    {
        $this->knownPermissions = [];
    }

    /**
     * @param Session $session
     * @param string|string[] $permissionNames
     */
    public function addPermission(Session $session, string|array $permissionNames): void
    {
        $xuid = $session->getPlayer()->getXuid();
        foreach ((array) $permissionNames as $permission) {
            if (!$this->isRegistered($permission)) {
                Kernel::getInstance()->getPlugin()->getLogger()->warning("PermissionUtils: Permission '$permission' not found.");
                continue;
            }
            $this->permissions[$xuid][$permission] = true;
        }
    }

    /**
     * @param Session $session
     * @param string|string[]|null $permissionNames
     */
    public function removePermission(Session $session, string|array|null $permissionNames = null): void
    {
        $xuid = $session->getPlayer()->getXuid();
        if ($permissionNames === null) {
            unset($this->permissions[$xuid]);
            return;
        }
        foreach ((array) $permissionNames as $permission) {
            if (isset($this->permissions[$xuid][$permission])) {
                unset($this->permissions[$xuid][$permission]);
            }
        }
        if (empty($this->permissions[$xuid])) {
            unset($this->permissions[$xuid]);
        }
    }

    public function hasPermission(Session $session, string $permission): bool
    {
        $xuid = $session->getPlayer()->getXuid();
        return isset($this->permissions[$xuid][$permission]);
    }

    /** @return string[] */
    public function getPermissions(): array
    {
        return array_keys($this->knownPermissions);
    }

    /** @return list<int|string> */
    public function getSessionPermissions(Session $session): array
    {
        $xuid = $session->getPlayer()->getXuid();
        return array_keys($this->permissions[$xuid] ?? []);
    }

    public function isRegistered(string $permission): bool
    {
        return isset($this->knownPermissions[$permission]);
    }
}