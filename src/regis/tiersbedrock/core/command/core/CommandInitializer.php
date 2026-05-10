<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\command\core;

use regis\tiersbedrock\core\command\commands\ForFeitCommand;
use regis\tiersbedrock\core\command\commands\BanCommand;
use regis\tiersbedrock\core\command\commands\BanSystemCommand;
use regis\tiersbedrock\core\command\commands\ControlCommand;
use regis\tiersbedrock\core\command\commands\DuelCommand;
use regis\tiersbedrock\core\command\commands\HubCommand;
use regis\tiersbedrock\core\command\commands\KickCommand;
use regis\tiersbedrock\core\command\commands\MsgCommand;
use regis\tiersbedrock\core\command\commands\MuteCommand;
use regis\tiersbedrock\core\command\commands\PingCommand;
use regis\tiersbedrock\core\command\commands\RankCommand;
use regis\tiersbedrock\core\command\commands\ReconnectCommand;
use regis\tiersbedrock\core\command\commands\RuleCommand;
use regis\tiersbedrock\core\command\commands\test\TestCommand;
use regis\tiersbedrock\core\command\commands\UnbanCommand;
use regis\tiersbedrock\core\command\commands\vanilla\EffectCommand;
use regis\tiersbedrock\core\command\commands\vanilla\EnumCommand;
use regis\tiersbedrock\core\command\commands\vanilla\GamemodeCommand;
use regis\tiersbedrock\core\command\commands\vanilla\GiveCommand;
use regis\tiersbedrock\core\command\commands\vanilla\TeleportCommand;
use pocketmine\network\mcpe\protocol\types\command\CommandHardEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandSoftEnum;
use pocketmine\Server;
use regis\tiersbedrock\core\kernel\Kernel;

final class CommandInitializer
{
    private const PERMISSIONS = [
        "tiers.command.enum",
    ];
    public static function init(): void
    {
        $commandMap = Server::getInstance()->getCommandMap();

        $commands = $commandMap->getCommands();
        foreach ($commands as $command) {
            $commandMap->unregister($command);
        }

        self::create();
        self::process();
        CommandEnum::getInstance()->init();
    }

    private static function create(): void
    {
        PermissionUtils::getInstance()->createPermission(self::PERMISSIONS);

        new TestCommand();
    }

    public static function process(): void
    {
        $commands = self::getCommandsByPlugin();
        $commandEnumsRaw = [];
        $softEnumsRaw = [];
        $hardEnumsRaw = [];
        $commandObjs = [];

        foreach ($commands as $command) {
            $commandObjs[$command->getName()] = $command;
        }

        foreach ($commandObjs as $commandName => $command) {
            /** @var BaseCommand $command */
            $commandEnumsRaw[$commandName] = $command->getEnums();
            foreach ($command->getSoftEnums() as $name => $values) {
                $softEnumsRaw[$name] = $values;
            }
            foreach ($command->getHardEnums() as $name => $values) {
                $hardEnumsRaw[$name] = $values;
            }
        }

        $softEnumObjs = [];
        $hardEnumObjs = [];
        foreach ($softEnumsRaw as $enumName => $values) {
            /** @var list<string> $values */
            $softEnumObjs[$enumName] = new CommandSoftEnum($enumName, $values);
        }
        foreach ($hardEnumsRaw as $enumName => $values) {
            /** @var list<string> $values */
            $hardEnumObjs[$enumName] = new CommandHardEnum($enumName, $values);
        }

        $commandEnum = CommandEnum::getInstance();
        $commandEnum->setSoftEnums($softEnumObjs);
        $commandEnum->setHardEnums($hardEnumObjs);

        foreach ($commandObjs as $command) {
            /** @var BaseCommand $command */
            $definitions = $commandEnumsRaw[$command->getName()] ?? [];
            $overloads = $commandEnum->generateOverloads($definitions);

            $data = $commandEnum->convertToCommandData($command, $overloads);
            $command->setCommandData($data);
        }
    }

    /** @return BaseCommand[] */
    public static function getCommandsByPlugin(): array
    {
        $result = [];
        $commandMap = Server::getInstance()->getCommandMap();
        $pluginName = Kernel::getInstance()->getPlugin()->getName();

        foreach ($commandMap->getCommands() as $command) {
            if ($command instanceof BaseCommand && $command->getOwningPlugin()->getName() === $pluginName) {
                $result[] = $command;
            }
        }
        return $result;
    }
}