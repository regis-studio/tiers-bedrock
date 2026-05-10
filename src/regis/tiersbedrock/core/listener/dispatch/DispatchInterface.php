<?php

declare(strict_types=1);

namespace regis\tiersbedrock\core\listener\dispatch;

// Block Events
use regis\tiersbedrock\core\session\Session;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockDeathEvent;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\block\BlockExplodeEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockItemPickupEvent;
use pocketmine\event\block\BlockMeltEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockPreExplodeEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\BlockTeleportEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\BrewingFuelUseEvent;
use pocketmine\event\block\BrewItemEvent;
use pocketmine\event\block\CampfireCookEvent;
use pocketmine\event\block\ChestPairEvent;
use pocketmine\event\block\FarmlandHydrationChangeEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\block\PressurePlateUpdateEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\StructureGrowEvent;

// Entity Events
use pocketmine\event\entity\AreaEffectCloudApplyEvent;
use pocketmine\event\entity\EntityBlockChangeEvent;
use pocketmine\event\entity\EntityCombustByBlockEvent;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityEffectEvent;
use pocketmine\event\entity\EntityEffectRemoveEvent;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityExtinguishEvent;
use pocketmine\event\entity\EntityFrostWalkerEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityPreExplodeEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\entity\ItemDespawnEvent;
use pocketmine\event\entity\ItemMergeEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;

// Inventory Events
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\inventory\FurnaceBurnEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;

// Player Events
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerBedLeaveEvent;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerBucketEvent; // Abstract
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDisplayNameChangeEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerDuplicateLoginEvent;
use pocketmine\event\player\PlayerEditBookEvent;
use pocketmine\event\player\PlayerEmoteEvent;
use pocketmine\event\player\PlayerEnchantingOptionsRequestEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerEntityPickEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemEnchantEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMissSwingEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPostChunkSendEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerResourcePackOfferEvent;
use pocketmine\event\player\PlayerRespawnAnchorUseEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\player\PlayerToggleGlideEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\player\PlayerToggleSprintEvent;
use pocketmine\event\player\PlayerToggleSwimEvent;
use pocketmine\event\player\PlayerTransferEvent;
use pocketmine\event\player\PlayerViewDistanceChangeEvent;

// Plugin Events
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\event\plugin\PluginEvent;

// Server Events
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\DataPacketDecodeEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\server\LowMemoryEvent;
use pocketmine\event\server\NetworkInterfaceEvent;
use pocketmine\event\server\NetworkInterfaceRegisterEvent;
use pocketmine\event\server\NetworkInterfaceUnregisterEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\event\server\ServerEvent;
use pocketmine\event\server\UpdateNotifyEvent;

// World Events
use pocketmine\event\world\ChunkEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\event\world\ChunkPopulateEvent;
use pocketmine\event\world\ChunkUnloadEvent;
use pocketmine\event\world\SpawnChangeEvent;
use pocketmine\event\world\WorldDifficultyChangeEvent;
use pocketmine\event\world\WorldDisplayNameChangeEvent;
use pocketmine\event\world\WorldEvent;
use pocketmine\event\world\WorldInitEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\event\world\WorldParticleEvent;
use pocketmine\event\world\WorldSaveEvent;
use pocketmine\event\world\WorldSoundEvent;
use pocketmine\event\world\WorldUnloadEvent;

interface DispatchInterface
{
    // --- Block Events ---

    public function handleBlockBreak(Session $session, BlockBreakEvent $event): void;
    public function handleBlockBurn(Session $session, BlockBurnEvent $event): void;
    public function handleBlockDeath(Session $session, BlockDeathEvent $event): void;
    public function handleBlockEvent(Session $session, BlockEvent $event): void;
    public function handleBlockExplode(Session $session, BlockExplodeEvent $event): void;
    public function handleBlockForm(Session $session, BlockFormEvent $event): void;
    public function handleBlockGrow(Session $session, BlockGrowEvent $event): void;
    public function handleBlockItemPickup(Session $session, BlockItemPickupEvent $event): void;
    public function handleBlockMelt(Session $session, BlockMeltEvent $event): void;
    public function handleBlockPlace(Session $session, BlockPlaceEvent $event): void;
    public function handleBlockPreExplode(Session $session, BlockPreExplodeEvent $event): void;
    public function handleBlockSpread(Session $session, BlockSpreadEvent $event): void;
    public function handleBlockTeleport(Session $session, BlockTeleportEvent $event): void;
    public function handleBlockUpdate(Session $session, BlockUpdateEvent $event): void;
    public function handleBrewingFuelUse(Session $session, BrewingFuelUseEvent $event): void;
    public function handleBrewItem(Session $session, BrewItemEvent $event): void;
    public function handleCampfireCook(Session $session, CampfireCookEvent $event): void;
    public function handleChestPair(Session $session, ChestPairEvent $event): void;
    public function handleFarmlandHydrationChange(Session $session, FarmlandHydrationChangeEvent $event): void;
    public function handleLeavesDecay(Session $session, LeavesDecayEvent $event): void;
    public function handlePressurePlateUpdate(Session $session, PressurePlateUpdateEvent $event): void;
    public function handleSignChange(Session $session, SignChangeEvent $event): void;
    public function handleStructureGrow(Session $session, StructureGrowEvent $event): void;

    // --- Entity Events ---
    public function handleAreaEffectCloudApply(Session $session, AreaEffectCloudApplyEvent $event): void;
    public function handleEntityBlockChange(Session $session, EntityBlockChangeEvent $event): void;
    public function handleEntityCombustByBlock(Session $session, EntityCombustByBlockEvent $event): void;
    public function handleEntityCombustByEntity(Session $session, EntityCombustByEntityEvent $event): void;
    public function handleEntityCombust(Session $session, EntityCombustEvent $event): void;
    public function handleEntityDamageByBlock(Session $session, EntityDamageByBlockEvent $event): void;
    public function handleEntityDamageByChildEntity(Session $session, EntityDamageByChildEntityEvent $event): void;
    public function handleEntityDamageByEntity(Session $session, EntityDamageByEntityEvent $event): void;
    public function handleEntityDamage(Session $session, EntityDamageEvent $event): void;
    public function handleEntityDeath(Session $session, EntityDeathEvent $event): void;
    public function handleEntityDespawn(Session $session, EntityDespawnEvent $event): void;
    public function handleEntityEffectAdd(Session $session, EntityEffectAddEvent $event): void;
    public function handleEntityEffect(Session $session, EntityEffectEvent $event): void;
    public function handleEntityEffectRemove(Session $session, EntityEffectRemoveEvent $event): void;
    public function handleEntityExplode(Session $session, EntityExplodeEvent $event): void;
    public function handleEntityExtinguish(Session $session, EntityExtinguishEvent $event): void;
    public function handleEntityFrostWalker(Session $session, EntityFrostWalkerEvent $event): void;
    public function handleEntityItemPickup(Session $session, EntityItemPickupEvent $event): void;
    public function handleEntityMotion(Session $session, EntityMotionEvent $event): void;
    public function handleEntityPreExplode(Session $session, EntityPreExplodeEvent $event): void;
    public function handleEntityRegainHealth(Session $session, EntityRegainHealthEvent $event): void;
    public function handleEntityShootBow(Session $session, EntityShootBowEvent $event): void;
    public function handleEntitySpawn(Session $session, EntitySpawnEvent $event): void;
    public function handleEntityTeleport(Session $session, EntityTeleportEvent $event): void;
    public function handleEntityTrampleFarmland(Session $session, EntityTrampleFarmlandEvent $event): void;
    public function handleItemDespawn(Session $session, ItemDespawnEvent $event): void;
    public function handleItemMerge(Session $session, ItemMergeEvent $event): void;
    public function handleItemSpawn(Session $session, ItemSpawnEvent $event): void;
    public function handleProjectileHitBlock(Session $session, ProjectileHitBlockEvent $event): void;
    public function handleProjectileHitEntity(Session $session, ProjectileHitEntityEvent $event): void;
    public function handleProjectileHit(Session $session, ProjectileHitEvent $event): void;
    public function handleProjectileLaunch(Session $session, ProjectileLaunchEvent $event): void;

    // --- Inventory Events ---
    public function handleCraftItem(Session $session, CraftItemEvent $event): void;
    public function handleFurnaceBurn(Session $session, FurnaceBurnEvent $event): void;
    public function handleFurnaceSmelt(Session $session, FurnaceSmeltEvent $event): void;
    public function handleInventoryClose(Session $session, InventoryCloseEvent $event): void;
    public function handleInventoryEvent(Session $session, InventoryEvent $event): void;
    public function handleInventoryOpen(Session $session, InventoryOpenEvent $event): void;
    public function handleInventoryTransaction(Session $session, InventoryTransactionEvent $event): void;

    // --- Player Events ---
    public function handlePlayerBedEnter(Session $session, PlayerBedEnterEvent $event): void;
    public function handlePlayerBedLeave(Session $session, PlayerBedLeaveEvent $event): void;
    public function handlePlayerBlockPick(Session $session, PlayerBlockPickEvent $event): void;
    public function handlePlayerBucketEmpty(Session $session, PlayerBucketEmptyEvent $event): void;
    public function handlePlayerBucketFill(Session $session, PlayerBucketFillEvent $event): void;
    public function handlePlayerBucket(Session $session, PlayerBucketEvent $event): void;
    public function handlePlayerChangeSkin(Session $session, PlayerChangeSkinEvent $event): void;
    public function handlePlayerChat(Session $session, PlayerChatEvent $event): void;
    public function handlePlayerCreation(Session $session, PlayerCreationEvent $event): void;
    public function handlePlayerDataSave(Session $session, PlayerDataSaveEvent $event): void;
    public function handlePlayerDeath(Session $session, PlayerDeathEvent $event): void;
    public function handlePlayerDisplayNameChange(Session $session, PlayerDisplayNameChangeEvent $event): void;
    public function handlePlayerDropItem(Session $session, PlayerDropItemEvent $event): void;
    public function handlePlayerDuplicateLogin(Session $session, PlayerDuplicateLoginEvent $event): void;
    public function handlePlayerEditBook(Session $session, PlayerEditBookEvent $event): void;
    public function handlePlayerEmote(Session $session, PlayerEmoteEvent $event): void;
    public function handlePlayerEnchantingOptionsRequest(Session $session, PlayerEnchantingOptionsRequestEvent $event): void;
    public function handlePlayerEntityInteract(Session $session, PlayerEntityInteractEvent $event): void;
    public function handlePlayerEntityPick(Session $session, PlayerEntityPickEvent $event): void;
    public function handlePlayerEvent(Session $session, PlayerEvent $event): void;
    public function handlePlayerExhaust(Session $session, PlayerExhaustEvent $event): void;
    public function handlePlayerExperienceChange(Session $session, PlayerExperienceChangeEvent $event): void;
    public function handlePlayerGameModeChange(Session $session, PlayerGameModeChangeEvent $event): void;
    public function handlePlayerInteract(Session $session, PlayerInteractEvent $event): void;
    public function handlePlayerItemConsume(Session $session, PlayerItemConsumeEvent $event): void;
    public function handlePlayerItemEnchant(Session $session, PlayerItemEnchantEvent $event): void;
    public function handlePlayerItemHeld(Session $session, PlayerItemHeldEvent $event): void;
    public function handlePlayerItemUse(Session $session, PlayerItemUseEvent $event): void;
    public function handlePlayerJoin(Session $session, PlayerJoinEvent $event): void;
    public function handlePlayerJump(Session $session, PlayerJumpEvent $event): void;
    public function handlePlayerKick(Session $session, PlayerKickEvent $event): void;
    public function handlePlayerLogin(Session $session, PlayerLoginEvent $event): void;
    public function handlePlayerMissSwing(Session $session, PlayerMissSwingEvent $event): void;
    public function handlePlayerMove(Session $session, PlayerMoveEvent $event): void;
    public function handlePlayerPostChunkSend(Session $session, PlayerPostChunkSendEvent $event): void;
    public function handlePlayerPreLogin(Session $session, PlayerPreLoginEvent $event): void;
    public function handlePlayerQuit(Session $session, PlayerQuitEvent $event): void;
    public function handlePlayerResourcePackOffer(Session $session, PlayerResourcePackOfferEvent $event): void;
    public function handlePlayerRespawnAnchorUse(Session $session, PlayerRespawnAnchorUseEvent $event): void;
    public function handlePlayerRespawn(Session $session, PlayerRespawnEvent $event): void;
    public function handlePlayerToggleFlight(Session $session, PlayerToggleFlightEvent $event): void;
    public function handlePlayerToggleGlide(Session $session, PlayerToggleGlideEvent $event): void;
    public function handlePlayerToggleSneak(Session $session, PlayerToggleSneakEvent $event): void;
    public function handlePlayerToggleSprint(Session $session, PlayerToggleSprintEvent $event): void;
    public function handlePlayerToggleSwim(Session $session, PlayerToggleSwimEvent $event): void;
    public function handlePlayerTransfer(Session $session, PlayerTransferEvent $event): void;
    public function handlePlayerViewDistanceChange(Session $session, PlayerViewDistanceChangeEvent $event): void;

    // --- Plugin Events ---
    public function handlePluginDisable(Session $session, PluginDisableEvent $event): void;
    public function handlePluginEnable(Session $session, PluginEnableEvent $event): void;
    public function handlePluginEvent(Session $session, PluginEvent $event): void;

    // --- Server Events ---
    public function handleCommand(Session $session, CommandEvent $event): void;
    public function handleDataPacketDecode(Session $session, DataPacketDecodeEvent $event): void;
    public function handleDataPacketReceive(Session $session, DataPacketReceiveEvent $event): void;
    public function handleDataPacketSend(Session $session, DataPacketSendEvent $event): void;
    public function handleLowMemory(Session $session, LowMemoryEvent $event): void;
    public function handleNetworkInterface(Session $session, NetworkInterfaceEvent $event): void;
    public function handleNetworkInterfaceRegister(Session $session, NetworkInterfaceRegisterEvent $event): void;
    public function handleNetworkInterfaceUnregister(Session $session, NetworkInterfaceUnregisterEvent $event): void;
    public function handleQueryRegenerate(Session $session, QueryRegenerateEvent $event): void;
    public function handleServerEvent(Session $session, ServerEvent $event): void;
    public function handleUpdateNotify(Session $session, UpdateNotifyEvent $event): void;

    // --- World Events ---
    public function handleChunkEvent(Session $session, ChunkEvent $event): void;
    public function handleChunkLoad(Session $session, ChunkLoadEvent $event): void;
    public function handleChunkPopulate(Session $session, ChunkPopulateEvent $event): void;
    public function handleChunkUnload(Session $session, ChunkUnloadEvent $event): void;
    public function handleSpawnChange(Session $session, SpawnChangeEvent $event): void;
    public function handleWorldDifficultyChange(Session $session, WorldDifficultyChangeEvent $event): void;
    public function handleWorldDisplayNameChange(Session $session, WorldDisplayNameChangeEvent $event): void;
    public function handleWorldEvent(Session $session, WorldEvent $event): void;
    public function handleWorldInit(Session $session, WorldInitEvent $event): void;
    public function handleWorldLoad(Session $session, WorldLoadEvent $event): void;
    public function handleWorldParticle(Session $session, WorldParticleEvent $event): void;
    public function handleWorldSave(Session $session, WorldSaveEvent $event): void;
    public function handleWorldSound(Session $session, WorldSoundEvent $event): void;
    public function handleWorldUnload(Session $session, WorldUnloadEvent $event): void;
}