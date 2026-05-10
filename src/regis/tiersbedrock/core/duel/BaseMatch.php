<?php

namespace regis\tiersbedrock\core\duel;

use pocketmine\player\Player;

abstract class BaseMatch
{
    protected Player $player1;
    protected Player $player2;
    protected BaseMode $mode;
    protected ?Player $winner = null;
    protected bool $canMove = false;

    protected bool $started = false;
    public function __construct(Player $player1, Player $player2, BaseMode $mode)
    {
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->mode = $mode;
    }

    public function start(): void
    {
        $this->started = true;
    }

    public function end(): void
    {
        $this->started = false;
    }

    public function canMove(): bool
    {
        return $this->canMove;
    }

    public function getPlayer1(): Player
    {
        return $this->player1;
    }

    public function getPlayers(): array
    {
        return [$this->player1, $this->player2];
    }

    public function getPlayer2(): Player
    {
        return $this->player2;
    }

    public function getOpponent(Player $player): Player
    {
        return $player === $this->player1 ? $this->player2 : $this->player1;
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function setWinner(Player $player): void
    {
        if ($this->player1 === $player || $this->player2 === $player) {
            $this->winner = $player;
        }
    }
}
