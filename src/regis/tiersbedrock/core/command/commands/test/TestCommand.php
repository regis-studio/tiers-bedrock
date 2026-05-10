<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\command\commands\test;

use regis\tiersbedrock\core\command\core\BaseCommand;
use regis\tiersbedrock\core\session\Session;

final class TestCommand extends BaseCommand
{
    public function __construct()
    {
        $this->setCanConsole(true);
        parent::__construct("test", "テストコマンド", "/test");
    }

    protected function onRun(Session $session, array $args): void
    {
        $session->getPlayer()->sendMessage("テストコマンドが実行されました！");
    }
}