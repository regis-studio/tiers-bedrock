<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\command\core;

use pocketmine\command\CommandSender;

trait UsageTrait
{
    public function getUsageText(): string
    {
        $commandName = mb_strtolower($this->getName());

        if (count($this->enums) === 1) {
            return "§cUsage: /{$commandName} " . $this->enums[0];
        }

        $message = "§cUsage:";
        foreach ($this->enums as $enum) {
            $message .= "\n/{$commandName} {$enum}";
        }

        return $message;
    }

    protected function showUsage(CommandSender $sender): void
    {
        $sender->sendMessage($this->getUsageText());
    }
}