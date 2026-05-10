<?php
namespace regis\tiersbedrock\core\duel;

abstract class BaseMode
{
    abstract function getName(): string;
    abstract function getDisplayName(): string;
    abstract function getKitName(): string;
    abstract function getMatchClass(): string;
}