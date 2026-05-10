<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\form\formapi;

use Closure;
use regis\tiersbedrock\core\session\Session;
use regis\tiersbedrock\core\session\SessionManager;
use regis\tiersbedrock\core\utils\ErrorReporter;
use pocketmine\event\CancellableTrait;
use pocketmine\form\Form;
use pocketmine\player\Player;
use Throwable;

abstract class ActionForm implements Form
{
    use CancellableTrait;
    private static array $initializedClasses = [];
    /** @var array{data: array, handler: array<int, Closure|null>} */
    private array $entries = [
        "data" => [],
        "handler" => []
    ];

    private string $title = "";
    private string $content = "";
    protected Session $session;

    protected function __construct(Session $session, mixed ...$args)
    {
        $className = static::class;
        if (!isset(self::$initializedClasses[$className])) {
            $this->init();
            self::$initializedClasses[$className] = true;
        }
        $this->session = $session;
        $this->onFormBuild($args[0] ?? []);
    }

    protected function init(): void
    {

    }

    public static function sendForm(Session $session, mixed ...$args): void
    {
        $instance = new static($session, ...$args);
        if ($instance->isCancelled()) {
            return;
        }
        $session->getPlayer()->sendForm($instance);
    }

    protected function setTitle(string $title): void
    {
        $this->title = $title;
    }

    protected function setContent(string $content): void
    {
        $this->content = $content;
    }

    protected function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @param string $text
     * @param string|null $image
     * @param Closure|null $handler
     */
    public function addButton(string $text, ?string $image = null, ?Closure $handler = null): void
    {
        $button = ["text" => $text];
        if ($image !== null) {
            if (strpos($image, "://") !== false) {
                $button["image"] = ["type" => "url", "data" => $image];
            } else {
                $button["image"] = ["type" => "path", "data" => $image];
            }
        }

        $this->entries["data"][] = $button;
        $this->entries["handler"][] = $handler;
    }

    abstract protected function onFormBuild(mixed ...$args): void;

    final public function handleResponse(Player $player, mixed $data): void
    {
        try {
            $session = SessionManager::getInstance()->getSession($player);
            if ($session === null) {
                return;
            }

            if ($data === null) {
                $this->onCancel($session);
                return;
            }

            if (!is_int($data) || !isset($this->entries["handler"][$data])) {
                return;
            }

            $handler = $this->entries["handler"][$data];

            ($handler)($session);
        } catch (Throwable $e) {
            ErrorReporter::getInstance()->onError($e, $player);
        }
    }

    protected function onCancel(Session $session): void
    {
    }

    /** @return array<string, mixed> */
    final public function jsonSerialize(): array
    {
        return [
            "type" => "form",
            "title" => $this->title,
            "content" => $this->content,
            "buttons" => $this->entries["data"]
        ];
    }
}