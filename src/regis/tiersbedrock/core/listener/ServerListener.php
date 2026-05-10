<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\listener;

use regis\tiersbedrock\core\command\CommandEnum;
use regis\tiersbedrock\core\command\CommandHandler;
use regis\tiersbedrock\core\server\Karnel;
use regis\tiersbedrock\core\session\constant\SessionQuitReason;
use regis\tiersbedrock\core\session\Session;
use regis\tiersbedrock\core\session\SessionManager;
use regis\tiersbedrock\core\utils\ErrorReporter;
use regis\tiersbedrock\core\world\chunk\ChunkLoad;
use pocketmine\block\Air;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Water;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\CommandRequestPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket;
use pocketmine\network\mcpe\protocol\SetTimePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\particle\BlockBreakParticle;
use Throwable;

final class ServerListener implements Listener
{

    public function onPlayerLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        $sessionManager = SessionManager::getInstance();
        if (!$sessionManager->canJoin($player)) {
            $player->kick("Internal Error");
            return;
        }
        $session = $sessionManager->createSession($player);
        $session->handleLogin();
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $sessionManager = SessionManager::getInstance();
        $session = $sessionManager->getPendingSession($player);
        if (!$session instanceof Session) {
            $player->kick("Internal Error");
            return;
        }
        $event->setJoinMessage("");
        $session->handleJoin();
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $sessionManager = SessionManager::getInstance();
        $session = $sessionManager->getExistingSession($player);
        if ($session instanceof Session) {
            $session->handleQuit(SessionQuitReason::CLIENT_DISCONNECT); //TODO: 種類によって変更
        }
        $sessionManager->destroySession($player);
        $event->setQuitMessage("");
    }

    //----------------------------------------------------------------------------------------------------------------------------

    private function processEvent(Player $player, Event $event, string $methodName, bool $loginRun = false): void
    {
        $session = SessionManager::getInstance()->getSession($player);

        if (!$session instanceof Session) {
            if ($event instanceof Cancellable) {
                /** @var PlayerChatEvent $event cancelでIDE上で警告うざいからね */
                if ($loginRun && SessionManager::getInstance()->getPendingSession($player) === null) {
                    $event->cancel();
                }
            }
            return;
        }

        try {
            $session->getHandler()->$methodName($session, $event);
        } catch (Throwable $e) {
            if ($event instanceof Cancellable) {
                /** @var PlayerChatEvent $event cancelでIDE上で警告うざいからね */
                $event->cancel();
            }
            ErrorReporter::getInstance()->onError($e);
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $this->processEvent($entity, $event, 'handleEntityDamage');
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $this->processEvent($event->getPlayer(), $event, 'handleBlockPlace');
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $this->processEvent($event->getPlayer(), $event, 'handleBlockBreak');
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void
    {
        $this->processEvent($event->getPlayer(), $event, 'handlePlayerInteract');
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event): void
    {
        $this->processEvent($event->getPlayer(), $event, 'handlePlayerItemUse');
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void
    {
        $this->processEvent($event->getPlayer(), $event, 'handlePlayerDropItem');
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            $this->processEvent($player, $event, 'handlePlayerExhaust');
        }
    }

    public function onProjectileLaunch(ProjectileLaunchEvent $event): void
    {
        $shooter = $event->getEntity()->getOwningEntity();
        if ($shooter instanceof Player) {
            $this->processEvent($shooter, $event, 'handleProjectileLaunch');
        }
    }

    public function onChunkLoad(ChunkLoadEvent $event): void
    {
        try {
            ChunkLoad::onChunkLoad($event);
        } catch (Throwable $e) {
            ErrorReporter::getInstance()->onError($e);
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        if ($packet instanceof CommandRequestPacket) {
            $event->cancel();
            $player = $event->getOrigin()->getPlayer();
            if ($player === null) {
                return;
            }
            $session = SessionManager::getInstance()->getSession($player);
            if ($session === null) {
                return;
            }
            CommandHandler::getInstance()->onCommand($session, $packet->command);
        }



        $player = $event->getOrigin()->getPlayer();
        if ($player === null) {
            return;
        }
        $session = SessionManager::getInstance()->getSession($player);
        if ($session === null) {
            return;
        }
        if ($packet instanceof PlayerAuthInputPacket) {
            $flags = $packet->getInputFlags();
            if ($flags->get(PlayerAuthInputFlags::MISSED_SWING)) {
                $session->addCps();
            }
        }
        if ($packet instanceof InventoryTransactionPacket) {
            $trData = $packet->trData;

            if ($trData instanceof UseItemOnEntityTransactionData) {
                $actionType = $trData->getActionType();
                if ($actionType === 1) {
                    $session->addCps();
                }
                return;
            }
        }
    }

    public function onDataPacketSend(DataPacketSendEvent $event): void
    {
        try {
            $packets = $event->getPackets();
            foreach ($packets as $k => $packet) {
                if ($packet instanceof ResourcePacksInfoPacket) {
                    $pk = ResourcePacksInfoPacket::create($packet->resourcePackEntries, $packet->mustAccept, $packet->hasAddons, $packet->hasScripts, $packet->getWorldTemplateId(), $packet->getWorldTemplateVersion(), false);
                    $packets[$k] = $pk;
                    continue;
                }
                if ($packet instanceof AvailableCommandsPacket) {
                    foreach ($event->getTargets() as $target) {
                        if ($target->getPlayer() === null) {
                            $packets[$k] = CommandEnum::getInstance()->emptyCommandPacket();
                            continue 2;
                        }
                    }
                }
                if ($packet instanceof StartGamePacket) {
                }
                if ($packet instanceof SetTimePacket) {
                    foreach ($event->getTargets() as $target) {
                        $player = $target->getPlayer();
                        if ($player === null) {
                            unset($packets[$k]);
                            continue 2;
                        }
                        if ($player->hasNoClientPredictions()) {
                            continue 2;
                        }
                        $session = SessionManager::getInstance()->getSession($player);
                        if ($session === null) {
                            unset($packets[$k]);
                            continue 2;
                        }
                        if (!$session->canChangeTime) {
                            unset($packets[$k]);
                        }
                    }
                }
            }
            $event->setPackets($packets);
        } catch (Throwable $e) {
            ErrorReporter::getInstance()->onError($e);
            $event->cancel();
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void
    {
        try {
            $player = $event->getPlayer();
            $session = SessionManager::getInstance()->getSession($player);
            $event->cancel();

            if (!$session instanceof Session) {
                return;
            }
            $session->onChat($event);
        } catch (Throwable $e) {
            ErrorReporter::getInstance()->onError($e, $event->getPlayer());
        }
    }

    public function onProjectileHit(ProjectileHitBlockEvent $event): void
    {
        $entity = $event->getEntity();
        $entity->flagForDespawn();
    }


    public function onBucketEmpty(PlayerBucketEmptyEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlockClicked();
        $position = $block->getPosition();
        $world = $position->getWorld();

        Karnel::getInstance()->getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($position, $world): void {

            $world->addParticle($position->add(0.5, 0.5, 0.5), new BlockBreakParticle($world->getBlock($position)));
            $world->setBlock($position, VanillaBlocks::AIR());
        }), 20 * 8);
        $offsets = [
            [0, 0, 1],  // North
            [0, 0, -1], // South
            [1, 0, 0],  // East
            [-1, 0, 0], // West
            [1, 0, 1],  // Northeast
            [1, 0, -1], // Southeast
            [-1, 0, 1], // Northwest
            [-1, 0, -1], // Southwest
        ];

        foreach ($offsets as $offset) {
            $surroundingPosition = $position->add($offset[0], $offset[1], $offset[2]);
            $block = $world->getBlock($surroundingPosition);
            if (!$block instanceof Air && !$block instanceof Water) {
                return;
            }
            Karnel::getInstance()->getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($surroundingPosition, $world): void {
                $world->setBlock($surroundingPosition, VanillaBlocks::AIR());
            }), 20 * 8);
        }
    }

    public function onBlockForm(BlockFormEvent $event): void
    {
        $block = $event->getBlock();
        $position = $block->getPosition();
        $world = $position->getWorld();
        Karnel::getInstance()->getPlugin()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($position, $world): void {
            if (!$world->isLoaded()) {
                return;
            }
            $world->addParticle($position->add(0.5, 0.5, 0.5), new BlockBreakParticle($world->getBlock($position)));
            $world->setBlock($position, VanillaBlocks::AIR());
        }), 20 * 8);
    }
}