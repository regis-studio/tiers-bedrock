<?php

declare(strict_types=1);
namespace regis\tiersbedrock\core\command\core;

use regis\tiersbedrock\core\config\impl\CoreConfigList;
use regis\tiersbedrock\core\config\impl\CoreConfigManager;
use regis\tiersbedrock\core\config\impl\entry\MainConfig;
use regis\tiersbedrock\core\server\config\ServerConfig;
use regis\tiersbedrock\core\session\Session;
use regis\tiersbedrock\core\session\SessionManager;
use regis\tiersbedrock\core\utils\ErrorReporter;
use pocketmine\command\CommandMap;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandStringHelper;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use Throwable;

final class CommandHandler
{
    use SingletonTrait;

    private const COMMAND_COOLTIME = 1;

    /** @var BaseCommand[] */
    private array $commands = [];

    public function register(BaseCommand $command): void
    {
        $this->commands[$command->getLabel()] = $command;
        foreach ($command->getAliases() as $aliases) {
            $this->commands[$aliases] = $command;
        }
    }

    /**
     * @param Session $session
     * @param string $commandLine
     * @param bool $message
     * @return bool
     */
    public function onCommand(Session $session, string $commandLine, bool $message = true): bool
    {
        /** @var MainConfig */
        $mainConfig = CoreConfigManager::getInstance()->get(CoreConfigList::MAIN);
        $args = CommandStringHelper::parseQuoteAware($commandLine);
        if (empty($args)) {
            return false;
        }
        $commandLabel = strtolower(ltrim(array_shift($args), '/'));
        $command = $this->commands[$commandLabel] ?? null;
        if ($command === null) {
            $commandMap = Server::getInstance()->getCommandMap();
            $pmcommand = $commandMap->getCommand($commandLabel);
            if ($pmcommand === null) {
                $session->getPlayer()->sendMessage("§l{$mainConfig->getColor()}{$mainConfig->getServerName()} §f»§r§7 Sorry, that command is unavailable.");
                return false;
            }
            Server::getInstance()->dispatchCommand($session->getPlayer(), str_replace("/", "", $commandLine));
            return true;
        }
        if (!$this->canExecute($session, $command)) {
            if ($message) {
                $session->getPlayer()->sendMessage("§l{$mainConfig->getColor()}{$mainConfig->getServerName()} §f»§r§7 Sorry, that command is unavailable.");
            }
            return false;
        }
        if ($message) {
            $diff = microtime(true) - $session->getTempData()->lastCommandTime;
            $limit = ($session->getTempData()->commandExecuteLimit + $diff) / 2;
            if ($limit < self::COMMAND_COOLTIME) {
                $session->getPlayer()->sendMessage("§c§l»§r§c You are sending commands too fast. Please wait.");
                return false;
            }
            $session->getTempData()->lastCommandTime = microtime(true);
            $session->getTempData()->commandExecuteLimit = min(2.0, $limit);
        }
        try {
            if ($message) {
                $command->onRun($session, $args);
            }
        } catch (Throwable $e) {
            ErrorReporter::getInstance()->onError($e, $session->getPlayer());
        }
        return true;
    }

    /**
     * @param Session $session
     * @param BaseCommand|null $command
     * @return bool
     */
    private function canExecute(Session $session, ?BaseCommand $command): bool
    {
        if (SessionManager::getInstance()->getSession($session->getPlayer()) === null) {
            return false;
        }
        if ($command === null) {
            return false;
        }
        if (!$session->hasPermission($command->getCommandPermission())) {
            return false;
        }
        return true;
    }

    /**
     * @param string $selector
     * @param CommandSender $sender
     * @return Session[]
     */
    /**
     * @param string $selector
     * @param CommandSender $sender
     * @return Session[]
     */
    public function applySelector(string $selector, CommandSender $sender): array
    {
        $targets = [];
        $sessions = SessionManager::getInstance()->getSessions();

        if (!str_starts_with($selector, '@')) {
            $player = $sender->getServer()->getPlayerExact($selector);
            if ($player !== null) {
                $session = SessionManager::getInstance()->getSession($player);
                if ($session !== null) {
                    $targets[] = $session;
                }
            }
            return $targets;
        }

        $type = substr($selector, 0, 2);
        $args = [];

        if (preg_match('/^@[a-z]\[(.*)\]$/', $selector, $matches)) {
            $argString = $matches[1];
            $explodedArgs = explode(',', $argString);
            foreach ($explodedArgs as $arg) {
                $kv = explode('=', $arg);
                if (count($kv) === 2) {
                    $args[trim($kv[0])] = trim($kv[1]);
                }
            }
        }

        switch ($type) {
            case '@a':
                $targets = $sessions;
                break;

            case '@r':
                if (count($sessions) > 0) {
                    $targets[] = $sessions[array_rand($sessions)];
                }
                break;

            case '@s':
            case '@p':
                if ($sender instanceof Player) {
                    $session = SessionManager::getInstance()->getSession($sender);
                    if ($session !== null) {
                        $targets[] = $session;
                    }
                }
                break;
        }

        if (isset($args['distance']) && $sender instanceof Player) {
            $rangeRaw = $args['distance'];
            $senderPos = $sender->getLocation();

            $targets = array_filter($targets, function (Session $session) use ($rangeRaw, $senderPos) {
                $player = $session->getPlayer();
                if ($player->getWorld() !== $senderPos->getWorld()) {
                    return false;
                }

                $dist = $senderPos->distance($player->getLocation());

                if (str_starts_with($rangeRaw, '..')) {
                    $max = (float) substr($rangeRaw, 2);
                    return $dist <= $max;
                } elseif (str_ends_with($rangeRaw, '..')) {
                    $min = (float) substr($rangeRaw, 0, -2);
                    return $dist >= $min;
                } elseif (str_contains($rangeRaw, '..')) {
                    [$min, $max] = explode('..', $rangeRaw);
                    return $dist >= (float) $min && $dist <= (float) $max;
                } else {
                    return (int) $dist === (int) $rangeRaw;
                }
            });
        }

        return array_values($targets);
    }
}
