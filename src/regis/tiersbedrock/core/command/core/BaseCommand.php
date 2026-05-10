<?php

declare(strict_types=1);
namespace regis\tiersbedrock\core\command\core;

use regis\tiersbedrock\core\kernel\Karnel;
use regis\tiersbedrock\core\session\Session;
use regis\tiersbedrock\core\utils\ErrorReporter;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandData;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

abstract class BaseCommand extends Command
{
    use UsageTrait;

    /** @var string[] */
    protected array $enums = [];
    /** @var array<string, mixed> */
    protected array $softEnums = [];
    /** @var array<string, mixed> */
    protected array $hardEnums = [];
    protected bool $isCanConsole = false;
    protected string $commandPermission;
    private CommandData $commandData;

    /**
     * @param string $name
     * @param string $description
     * @param string $permission
     * @param string[] $aliases
     */
    public function __construct(string $name, string $description, string $permission, array $aliases = [])
    {
        $softEnums = [];
        $this->commandPermission = $permission;
        $this->setPermission("pocketmine.group.user");
        // @phpstan-ignore-next-line
        parent::__construct($name, $description, null, $aliases);
        Server::getInstance()->getCommandMap()->register('Practice', $this);
        CommandHandler::getInstance()->register($this);
    }

    public function getOwningPlugin(): PluginBase
    {
        return Karnel::getInstance()->getPlugin();
    }

    public function setCanConsole(bool $bool): void
    {
        $this->isCanConsole = $bool;
    }

    public function getCommandPermission(): string
    {
        return $this->commandPermission;
    }

    /** @return string[] */
    public function getEnums(): array
    {
        return $this->enums;
    }

    /** @return string[] */
    public function getSoftEnums(): array
    {
        return $this->softEnums;
    }

    /** @return string[] */
    public function getHardEnums(): array
    {
        return $this->hardEnums;
    }

    public function setCommandData(CommandData $commandData): void
    {
        $this->commandData = $commandData;
    }

    public function getCommandData(): CommandData
    {
        return $this->commandData;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        try {
            if ($sender->getName() === "CONSOLE") {
                if ($this->isCanConsole) {
                    $this->onConsole($sender, $args);
                } else {
                    $sender->sendMessage('§cThis command cannot be run from the console');
                }
            }
        } catch (\Throwable $e) {
            ErrorReporter::getInstance()->onError($e);
        }
    }

    /**
     * @param Session $session
     * @param string[] $args
     * @return void
     */
    abstract function onRun(Session $session, array $args): void;

    /**
     * @param CommandSender $sender
     * @param string[] $args
     * @return void
     */
    protected function onConsole(CommandSender $sender, array $args): void
    {
    }
}
