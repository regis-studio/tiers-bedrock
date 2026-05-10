<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\command\core;

use regis\tiersbedrock\core\kernel\Karnel;
use regis\tiersbedrock\core\server\logger\ConsoleLogger;
use regis\tiersbedrock\core\session\Session;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\serializer\AvailableCommandsPacketAssembler;
use pocketmine\network\mcpe\protocol\types\command\CommandData;
use pocketmine\network\mcpe\protocol\types\command\CommandHardEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandOverload;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\network\mcpe\protocol\types\command\CommandPermissions;
use pocketmine\network\mcpe\protocol\types\command\CommandSoftEnum;
use pocketmine\utils\SingletonTrait;

final class CommandEnum
{
    use SingletonTrait;

    /** @var list<CommandSoftEnum> */
    private array $softEnums = [];
    /** @var CommandHardEnum[] */
    private array $hardEnums = [];
    /** @var CommandHardEnum[] */
    private array $literalEnums = [];

    /** @var array<string, mixed> */
    private array $commandsByPermission = [];

    private const TYPE_MAPPING = [
        "int" => AvailableCommandsPacket::ARG_TYPE_INT,
        "float" => AvailableCommandsPacket::ARG_TYPE_FLOAT,
        "value" => AvailableCommandsPacket::ARG_TYPE_VALUE,
        "wildcard_int" => AvailableCommandsPacket::ARG_TYPE_WILDCARD_INT,
        "op" => AvailableCommandsPacket::ARG_TYPE_OPERATOR,
        "target" => AvailableCommandsPacket::ARG_TYPE_TARGET,
        "wildcard_target" => AvailableCommandsPacket::ARG_TYPE_WILDCARD_TARGET,
        "string" => AvailableCommandsPacket::ARG_TYPE_STRING,
        "filepath" => AvailableCommandsPacket::ARG_TYPE_FILEPATH,
        "position" => AvailableCommandsPacket::ARG_TYPE_POSITION,
        "message" => AvailableCommandsPacket::ARG_TYPE_MESSAGE,
        "rawtext" => AvailableCommandsPacket::ARG_TYPE_RAWTEXT,
        "json" => AvailableCommandsPacket::ARG_TYPE_JSON,
        "text" => AvailableCommandsPacket::ARG_TYPE_RAWTEXT,
        "command" => AvailableCommandsPacket::ARG_TYPE_COMMAND,
        "block_states" => AvailableCommandsPacket::ARG_TYPE_BLOCK_STATES,
    ];

    public function init(): void
    {
        $commands = CommandInitializer::getCommandsByPlugin();
        foreach ($commands as $command) {
            $this->commandsByPermission[$command->getCommandPermission()][] = $command;
        }
    }

    public function emptyCommandPacket(): AvailableCommandsPacket
    {
        $packet = AvailableCommandsPacket::create([], [], [], [], [], [], [], []);
        return $packet;
    }

    public function updateCommand(Session $session): void
    {
        $commands = [];
        $commandDatas = [];

        $permissions = PermissionUtils::getInstance()->getSessionPermissions($session);

        foreach ($permissions as $permission) {
            $commands = array_merge($commands, $this->commandsByPermission[$permission] ?? []);
        }

        $commands = array_unique($commands, SORT_REGULAR);

        foreach ($commands as $command) {
            /** @var BaseCommand $command */
            $data = $command->getCommandData();
            $commandDatas[$data->name] = $data;
        }

        /** @var list<CommandHardEnum> */
        $allHardEnums = array_merge($this->hardEnums, $this->literalEnums);
        $packet = AvailableCommandsPacketAssembler::assemble(
            array_values($commandDatas),
            $allHardEnums,
            $this->softEnums
        );

        $session->getPlayer()->getNetworkSession()->sendDataPacket($packet);
    }

    /**
     * @param CommandHardEnum[] $hardEnums
     * @return void
     */
    public function setHardEnums(array $hardEnums): void
    {
        $this->hardEnums = $hardEnums;
    }

    /**
     * @param CommandSoftEnum[] $softEnums
     * @return void
     */
    public function setSoftEnums(array $softEnums): void
    {
        // @phpstan-ignore assign.propertyType
        $this->softEnums = $softEnums;
    }
    /** @return CommandHardEnum[] */
    public function getHardEnums(): array
    {
        return $this->hardEnums;
    }

    /** @return CommandSoftEnum[] */
    public function getSoftEnums(): array
    {
        return $this->softEnums;
    }

    private function getLiteralEnum(string $name): CommandHardEnum
    {
        if (!isset($this->literalEnums[$name])) {
            $this->literalEnums[$name] = new CommandHardEnum($name, [$name]);
        }
        return $this->literalEnums[$name];
    }

    /**
     * @param BaseCommand $command
     * @param CommandOverload[] $overloads
     * @return CommandData
     */
    public function convertToCommandData(BaseCommand $command, array $overloads): CommandData
    {
        $labelName = strtolower($command->getLabel());
        $aliases = $command->getAliases();
        $aliasEnum = null;

        if (count($aliases) > 0) {
            if (!in_array($labelName, $aliases, true)) {
                $aliases[] = $labelName;
            }
            $aliasEnum = new CommandHardEnum(ucfirst($command->getLabel()) . "Aliases", $aliases);
        }

        $desc = $command->getDescription();
        if (!is_string($desc)) {
            $desc = $desc->getText();
        }
        $data = new CommandData(
            $labelName,
            $desc,
            0,
            CommandPermissions::NORMAL,
            $aliasEnum,
            $overloads,
            []
        );

        return $data;
    }

    /**
     * @param array<mixed> $definitions
     * @return CommandOverload[]
     */
    public function generateOverloads(array $definitions): array
    {
        $overloads = [];

        foreach ($definitions as $definition) {
            $parts = explode(" ", $definition);
            $params = [];

            foreach ($parts as $part) {
                if ($part === "") {
                    continue;
                }

                if (preg_match('/^(?:<([^:]+):([^>]+)>|\[([^:]+):([^\]]+)\])$/', $part, $matches)) {
                    if (isset($matches[3])) {
                        $name = $matches[3];
                        $type = $matches[4];
                        $isOptional = true;
                    } else {
                        $name = $matches[1];
                        $type = $matches[2];
                        $isOptional = false;
                    }

                    if (isset(self::TYPE_MAPPING[$type])) {
                        $params[] = CommandParameter::standard($name, self::TYPE_MAPPING[$type], 0, $isOptional);
                        // @phpstan-ignore isset.offset
                    } elseif (isset($this->softEnums[$type])) {
                        $params[] = CommandParameter::softEnum($name, $this->softEnums[$type], 0, $isOptional);
                    } elseif (isset($this->hardEnums[$type])) {
                        $params[] = CommandParameter::enum($name, $this->hardEnums[$type], 0, $isOptional);
                    } else {
                        Karnel::getInstance()->getPlugin()->getLogger()->warning("CommandEnum: Type '$type' not found. Fallback to string.");
                        $params[] = CommandParameter::standard($name, AvailableCommandsPacket::ARG_TYPE_STRING, 0, $isOptional);
                    }
                } else {
                    $params[] = CommandParameter::enum($part, $this->getLiteralEnum($part), 0);
                }
            }
            $overloads[] = new CommandOverload(true, $params);
        }

        return $overloads;
    }
}