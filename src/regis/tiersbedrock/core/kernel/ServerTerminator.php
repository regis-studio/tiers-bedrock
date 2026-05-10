<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\kernel;

final class ServerTerminator
{
    public function terminate(): void
    {
        // サーバーを安全に終了させるための処理をここに実装します。
        // 例えば、プレイヤーへの通知、データの保存、リソースの解放などが考えられます。
    }

    public function transferPlayers(): void
    {
    }
}