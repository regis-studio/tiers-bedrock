<?php
declare(strict_types=1);
namespace regis\tiersbedrock\core\listener\dispatch;

// Block Events
use regis\tiersbedrock\core\kit\event\PlayerApplyKitEvent;
use regis\tiersbedrock\core\session\Session;
use pocketmine\event\block\BaseBlockChangeEvent;
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

trait DispatchTrait
{
    // ==========================================
    // Block Events
    // ==========================================

    final public function handleBlockBreak(Session $session, BlockBreakEvent $event): void
    {
        $this->onBlockBreak($session, $event);
    }
    protected function onBlockBreak(Session $session, BlockBreakEvent $event): void
    {
    }

    final public function handleBlockBurn(Session $session, BlockBurnEvent $event): void
    {
        $this->onBlockBurn($session, $event);
    }
    protected function onBlockBurn(Session $session, BlockBurnEvent $event): void
    {
    }

    final public function handleBlockDeath(Session $session, BlockDeathEvent $event): void
    {
        $this->onBlockDeath($session, $event);
    }
    protected function onBlockDeath(Session $session, BlockDeathEvent $event): void
    {
    }

    final public function handleBlockEvent(Session $session, BlockEvent $event): void
    {
        $this->onBlockEvent($session, $event);
    }
    protected function onBlockEvent(Session $session, BlockEvent $event): void
    {
    }

    final public function handleBlockExplode(Session $session, BlockExplodeEvent $event): void
    {
        $this->onBlockExplode($session, $event);
    }
    protected function onBlockExplode(Session $session, BlockExplodeEvent $event): void
    {
    }

    final public function handleBlockForm(Session $session, BlockFormEvent $event): void
    {
        $this->onBlockForm($session, $event);
    }
    protected function onBlockForm(Session $session, BlockFormEvent $event): void
    {
    }

    final public function handleBlockGrow(Session $session, BlockGrowEvent $event): void
    {
        $this->onBlockGrow($session, $event);
    }
    protected function onBlockGrow(Session $session, BlockGrowEvent $event): void
    {
    }

    final public function handleBlockItemPickup(Session $session, BlockItemPickupEvent $event): void
    {
        $this->onBlockItemPickup($session, $event);
    }
    protected function onBlockItemPickup(Session $session, BlockItemPickupEvent $event): void
    {
    }

    final public function handleBlockMelt(Session $session, BlockMeltEvent $event): void
    {
        $this->onBlockMelt($session, $event);
    }
    protected function onBlockMelt(Session $session, BlockMeltEvent $event): void
    {
    }

    final public function handleBlockPlace(Session $session, BlockPlaceEvent $event): void
    {
        $this->onBlockPlace($session, $event);
    }
    protected function onBlockPlace(Session $session, BlockPlaceEvent $event): void
    {
    }

    final public function handleBlockPreExplode(Session $session, BlockPreExplodeEvent $event): void
    {
        $this->onBlockPreExplode($session, $event);
    }
    protected function onBlockPreExplode(Session $session, BlockPreExplodeEvent $event): void
    {
    }

    final public function handleBlockSpread(Session $session, BlockSpreadEvent $event): void
    {
        $this->onBlockSpread($session, $event);
    }
    protected function onBlockSpread(Session $session, BlockSpreadEvent $event): void
    {
    }

    final public function handleBlockTeleport(Session $session, BlockTeleportEvent $event): void
    {
        $this->onBlockTeleport($session, $event);
    }
    protected function onBlockTeleport(Session $session, BlockTeleportEvent $event): void
    {
    }

    final public function handleBlockUpdate(Session $session, BlockUpdateEvent $event): void
    {
        $this->onBlockUpdate($session, $event);
    }
    protected function onBlockUpdate(Session $session, BlockUpdateEvent $event): void
    {
    }

    final public function handleBrewingFuelUse(Session $session, BrewingFuelUseEvent $event): void
    {
        $this->onBrewingFuelUse($session, $event);
    }
    protected function onBrewingFuelUse(Session $session, BrewingFuelUseEvent $event): void
    {
    }

    final public function handleBrewItem(Session $session, BrewItemEvent $event): void
    {
        $this->onBrewItem($session, $event);
    }
    protected function onBrewItem(Session $session, BrewItemEvent $event): void
    {
    }

    final public function handleCampfireCook(Session $session, CampfireCookEvent $event): void
    {
        $this->onCampfireCook($session, $event);
    }
    protected function onCampfireCook(Session $session, CampfireCookEvent $event): void
    {
    }

    final public function handleChestPair(Session $session, ChestPairEvent $event): void
    {
        $this->onChestPair($session, $event);
    }
    protected function onChestPair(Session $session, ChestPairEvent $event): void
    {
    }

    final public function handleFarmlandHydrationChange(Session $session, FarmlandHydrationChangeEvent $event): void
    {
        $this->onFarmlandHydrationChange($session, $event);
    }
    protected function onFarmlandHydrationChange(Session $session, FarmlandHydrationChangeEvent $event): void
    {
    }

    final public function handleLeavesDecay(Session $session, LeavesDecayEvent $event): void
    {
        $this->onLeavesDecay($session, $event);
    }
    protected function onLeavesDecay(Session $session, LeavesDecayEvent $event): void
    {
    }

    final public function handlePressurePlateUpdate(Session $session, PressurePlateUpdateEvent $event): void
    {
        $this->onPressurePlateUpdate($session, $event);
    }
    protected function onPressurePlateUpdate(Session $session, PressurePlateUpdateEvent $event): void
    {
    }

    final public function handleSignChange(Session $session, SignChangeEvent $event): void
    {
        $this->onSignChange($session, $event);
    }
    protected function onSignChange(Session $session, SignChangeEvent $event): void
    {
    }

    final public function handleStructureGrow(Session $session, StructureGrowEvent $event): void
    {
        $this->onStructureGrow($session, $event);
    }
    protected function onStructureGrow(Session $session, StructureGrowEvent $event): void
    {
    }


    // ==========================================
    // Entity Events
    // ==========================================
    final public function handleAreaEffectCloudApply(Session $session, AreaEffectCloudApplyEvent $event): void
    {
        $this->onAreaEffectCloudApply($session, $event);
    }
    protected function onAreaEffectCloudApply(Session $session, AreaEffectCloudApplyEvent $event): void
    {
    }

    final public function handleEntityBlockChange(Session $session, EntityBlockChangeEvent $event): void
    {
        $this->onEntityBlockChange($session, $event);
    }
    protected function onEntityBlockChange(Session $session, EntityBlockChangeEvent $event): void
    {
    }

    final public function handleEntityCombustByBlock(Session $session, EntityCombustByBlockEvent $event): void
    {
        $this->onEntityCombustByBlock($session, $event);
    }
    protected function onEntityCombustByBlock(Session $session, EntityCombustByBlockEvent $event): void
    {
    }

    final public function handleEntityCombustByEntity(Session $session, EntityCombustByEntityEvent $event): void
    {
        $this->onEntityCombustByEntity($session, $event);
    }
    protected function onEntityCombustByEntity(Session $session, EntityCombustByEntityEvent $event): void
    {
    }

    final public function handleEntityCombust(Session $session, EntityCombustEvent $event): void
    {
        $this->onEntityCombust($session, $event);
    }
    protected function onEntityCombust(Session $session, EntityCombustEvent $event): void
    {
    }

    final public function handleEntityDamageByBlock(Session $session, EntityDamageByBlockEvent $event): void
    {
        $this->onEntityDamageByBlock($session, $event);
    }
    protected function onEntityDamageByBlock(Session $session, EntityDamageByBlockEvent $event): void
    {
    }

    final public function handleEntityDamageByChildEntity(Session $session, EntityDamageByChildEntityEvent $event): void
    {
        $this->onEntityDamageByChildEntity($session, $event);
    }
    protected function onEntityDamageByChildEntity(Session $session, EntityDamageByChildEntityEvent $event): void
    {
    }

    final public function handleEntityDamageByEntity(Session $session, EntityDamageByEntityEvent $event): void
    {
        $this->onEntityDamageByEntity($session, $event);
    }
    protected function onEntityDamageByEntity(Session $session, EntityDamageByEntityEvent $event): void
    {
    }

    final public function handleEntityDamage(Session $session, EntityDamageEvent $event): void
    {
        $this->onEntityDamage($session, $event);
    }
    protected function onEntityDamage(Session $session, EntityDamageEvent $event): void
    {
    }

    final public function handleEntityDeath(Session $session, EntityDeathEvent $event): void
    {
        $this->onEntityDeath($session, $event);
    }
    protected function onEntityDeath(Session $session, EntityDeathEvent $event): void
    {
    }

    final public function handleEntityDespawn(Session $session, EntityDespawnEvent $event): void
    {
        $this->onEntityDespawn($session, $event);
    }
    protected function onEntityDespawn(Session $session, EntityDespawnEvent $event): void
    {
    }

    final public function handleEntityEffectAdd(Session $session, EntityEffectAddEvent $event): void
    {
        $this->onEntityEffectAdd($session, $event);
    }
    protected function onEntityEffectAdd(Session $session, EntityEffectAddEvent $event): void
    {
    }

    final public function handleEntityEffect(Session $session, EntityEffectEvent $event): void
    {
        $this->onEntityEffect($session, $event);
    }
    protected function onEntityEffect(Session $session, EntityEffectEvent $event): void
    {
    }

    final public function handleEntityEffectRemove(Session $session, EntityEffectRemoveEvent $event): void
    {
        $this->onEntityEffectRemove($session, $event);
    }
    protected function onEntityEffectRemove(Session $session, EntityEffectRemoveEvent $event): void
    {
    }

    final public function handleEntityExplode(Session $session, EntityExplodeEvent $event): void
    {
        $this->onEntityExplode($session, $event);
    }
    protected function onEntityExplode(Session $session, EntityExplodeEvent $event): void
    {
    }

    final public function handleEntityExtinguish(Session $session, EntityExtinguishEvent $event): void
    {
        $this->onEntityExtinguish($session, $event);
    }
    protected function onEntityExtinguish(Session $session, EntityExtinguishEvent $event): void
    {
    }

    final public function handleEntityFrostWalker(Session $session, EntityFrostWalkerEvent $event): void
    {
        $this->onEntityFrostWalker($session, $event);
    }
    protected function onEntityFrostWalker(Session $session, EntityFrostWalkerEvent $event): void
    {
    }

    final public function handleEntityItemPickup(Session $session, EntityItemPickupEvent $event): void
    {
        $this->onEntityItemPickup($session, $event);
    }
    protected function onEntityItemPickup(Session $session, EntityItemPickupEvent $event): void
    {
    }

    final public function handleEntityMotion(Session $session, EntityMotionEvent $event): void
    {
        $this->onEntityMotion($session, $event);
    }
    protected function onEntityMotion(Session $session, EntityMotionEvent $event): void
    {
    }

    final public function handleEntityPreExplode(Session $session, EntityPreExplodeEvent $event): void
    {
        $this->onEntityPreExplode($session, $event);
    }
    protected function onEntityPreExplode(Session $session, EntityPreExplodeEvent $event): void
    {
    }

    final public function handleEntityRegainHealth(Session $session, EntityRegainHealthEvent $event): void
    {
        $this->onEntityRegainHealth($session, $event);
    }
    protected function onEntityRegainHealth(Session $session, EntityRegainHealthEvent $event): void
    {
    }

    final public function handleEntityShootBow(Session $session, EntityShootBowEvent $event): void
    {
        $this->onEntityShootBow($session, $event);
    }
    protected function onEntityShootBow(Session $session, EntityShootBowEvent $event): void
    {
    }

    final public function handleEntitySpawn(Session $session, EntitySpawnEvent $event): void
    {
        $this->onEntitySpawn($session, $event);
    }
    protected function onEntitySpawn(Session $session, EntitySpawnEvent $event): void
    {
    }

    final public function handleEntityTeleport(Session $session, EntityTeleportEvent $event): void
    {
        $this->onEntityTeleport($session, $event);
    }
    protected function onEntityTeleport(Session $session, EntityTeleportEvent $event): void
    {
    }

    final public function handleEntityTrampleFarmland(Session $session, EntityTrampleFarmlandEvent $event): void
    {
        $this->onEntityTrampleFarmland($session, $event);
    }
    protected function onEntityTrampleFarmland(Session $session, EntityTrampleFarmlandEvent $event): void
    {
    }

    final public function handleItemDespawn(Session $session, ItemDespawnEvent $event): void
    {
        $this->onItemDespawn($session, $event);
    }
    protected function onItemDespawn(Session $session, ItemDespawnEvent $event): void
    {
    }

    final public function handleItemMerge(Session $session, ItemMergeEvent $event): void
    {
        $this->onItemMerge($session, $event);
    }
    protected function onItemMerge(Session $session, ItemMergeEvent $event): void
    {
    }

    final public function handleItemSpawn(Session $session, ItemSpawnEvent $event): void
    {
        $this->onItemSpawn($session, $event);
    }
    protected function onItemSpawn(Session $session, ItemSpawnEvent $event): void
    {
    }

    final public function handleProjectileHitBlock(Session $session, ProjectileHitBlockEvent $event): void
    {
        $this->onProjectileHitBlock($session, $event);
    }
    protected function onProjectileHitBlock(Session $session, ProjectileHitBlockEvent $event): void
    {
    }

    final public function handleProjectileHitEntity(Session $session, ProjectileHitEntityEvent $event): void
    {
        $this->onProjectileHitEntity($session, $event);
    }
    protected function onProjectileHitEntity(Session $session, ProjectileHitEntityEvent $event): void
    {
    }

    final public function handleProjectileHit(Session $session, ProjectileHitEvent $event): void
    {
        $this->onProjectileHit($session, $event);
    }
    protected function onProjectileHit(Session $session, ProjectileHitEvent $event): void
    {
    }

    final public function handleProjectileLaunch(Session $session, ProjectileLaunchEvent $event): void
    {
        $this->onProjectileLaunch($session, $event);
    }
    protected function onProjectileLaunch(Session $session, ProjectileLaunchEvent $event): void
    {
    }


    // ==========================================
    // Inventory Events
    // ==========================================
    final public function handleCraftItem(Session $session, CraftItemEvent $event): void
    {
        $this->onCraftItem($session, $event);
    }
    protected function onCraftItem(Session $session, CraftItemEvent $event): void
    {
    }

    final public function handleFurnaceBurn(Session $session, FurnaceBurnEvent $event): void
    {
        $this->onFurnaceBurn($session, $event);
    }
    protected function onFurnaceBurn(Session $session, FurnaceBurnEvent $event): void
    {
    }

    final public function handleFurnaceSmelt(Session $session, FurnaceSmeltEvent $event): void
    {
        $this->onFurnaceSmelt($session, $event);
    }
    protected function onFurnaceSmelt(Session $session, FurnaceSmeltEvent $event): void
    {
    }

    final public function handleInventoryClose(Session $session, InventoryCloseEvent $event): void
    {
        $this->onInventoryClose($session, $event);
    }
    protected function onInventoryClose(Session $session, InventoryCloseEvent $event): void
    {
    }

    final public function handleInventoryEvent(Session $session, InventoryEvent $event): void
    {
        $this->onInventoryEvent($session, $event);
    }
    protected function onInventoryEvent(Session $session, InventoryEvent $event): void
    {
    }

    final public function handleInventoryOpen(Session $session, InventoryOpenEvent $event): void
    {
        $this->onInventoryOpen($session, $event);
    }
    protected function onInventoryOpen(Session $session, InventoryOpenEvent $event): void
    {
    }

    final public function handleInventoryTransaction(Session $session, InventoryTransactionEvent $event): void
    {
        $this->onInventoryTransaction($session, $event);
    }
    protected function onInventoryTransaction(Session $session, InventoryTransactionEvent $event): void
    {
    }


    // ==========================================
    // Player Events
    // ==========================================
    final public function handlePlayerBedEnter(Session $session, PlayerBedEnterEvent $event): void
    {
        $this->onPlayerBedEnter($session, $event);
    }
    protected function onPlayerBedEnter(Session $session, PlayerBedEnterEvent $event): void
    {
    }

    final public function handlePlayerBedLeave(Session $session, PlayerBedLeaveEvent $event): void
    {
        $this->onPlayerBedLeave($session, $event);
    }
    protected function onPlayerBedLeave(Session $session, PlayerBedLeaveEvent $event): void
    {
    }

    final public function handlePlayerBlockPick(Session $session, PlayerBlockPickEvent $event): void
    {
        $this->onPlayerBlockPick($session, $event);
    }
    protected function onPlayerBlockPick(Session $session, PlayerBlockPickEvent $event): void
    {
    }

    final public function handlePlayerBucketEmpty(Session $session, PlayerBucketEmptyEvent $event): void
    {
        $this->onPlayerBucketEmpty($session, $event);
    }
    protected function onPlayerBucketEmpty(Session $session, PlayerBucketEmptyEvent $event): void
    {
    }

    final public function handlePlayerBucketFill(Session $session, PlayerBucketFillEvent $event): void
    {
        $this->onPlayerBucketFill($session, $event);
    }
    protected function onPlayerBucketFill(Session $session, PlayerBucketFillEvent $event): void
    {
    }

    final public function handlePlayerBucket(Session $session, PlayerBucketEvent $event): void
    {
        $this->onPlayerBucket($session, $event);
    }
    protected function onPlayerBucket(Session $session, PlayerBucketEvent $event): void
    {
    }

    final public function handlePlayerChangeSkin(Session $session, PlayerChangeSkinEvent $event): void
    {
        $this->onPlayerChangeSkin($session, $event);
    }
    protected function onPlayerChangeSkin(Session $session, PlayerChangeSkinEvent $event): void
    {
    }

    final public function handlePlayerChat(Session $session, PlayerChatEvent $event): void
    {
        $this->onPlayerChat($session, $event);
    }
    protected function onPlayerChat(Session $session, PlayerChatEvent $event): void
    {
    }

    final public function handlePlayerCreation(Session $session, PlayerCreationEvent $event): void
    {
        $this->onPlayerCreation($session, $event);
    }
    protected function onPlayerCreation(Session $session, PlayerCreationEvent $event): void
    {
    }

    final public function handlePlayerDataSave(Session $session, PlayerDataSaveEvent $event): void
    {
        $this->onPlayerDataSave($session, $event);
    }
    protected function onPlayerDataSave(Session $session, PlayerDataSaveEvent $event): void
    {
    }

    final public function handlePlayerDeath(Session $session, PlayerDeathEvent $event): void
    {
        $this->onPlayerDeath($session, $event);
    }
    protected function onPlayerDeath(Session $session, PlayerDeathEvent $event): void
    {
    }

    final public function handlePlayerDisplayNameChange(Session $session, PlayerDisplayNameChangeEvent $event): void
    {
        $this->onPlayerDisplayNameChange($session, $event);
    }
    protected function onPlayerDisplayNameChange(Session $session, PlayerDisplayNameChangeEvent $event): void
    {
    }

    final public function handlePlayerDropItem(Session $session, PlayerDropItemEvent $event): void
    {
        $this->onPlayerDropItem($session, $event);
    }
    protected function onPlayerDropItem(Session $session, PlayerDropItemEvent $event): void
    {
    }

    final public function handlePlayerDuplicateLogin(Session $session, PlayerDuplicateLoginEvent $event): void
    {
        $this->onPlayerDuplicateLogin($session, $event);
    }
    protected function onPlayerDuplicateLogin(Session $session, PlayerDuplicateLoginEvent $event): void
    {
    }

    final public function handlePlayerEditBook(Session $session, PlayerEditBookEvent $event): void
    {
        $this->onPlayerEditBook($session, $event);
    }
    protected function onPlayerEditBook(Session $session, PlayerEditBookEvent $event): void
    {
    }

    final public function handlePlayerEmote(Session $session, PlayerEmoteEvent $event): void
    {
        $this->onPlayerEmote($session, $event);
    }
    protected function onPlayerEmote(Session $session, PlayerEmoteEvent $event): void
    {
    }

    final public function handlePlayerEnchantingOptionsRequest(Session $session, PlayerEnchantingOptionsRequestEvent $event): void
    {
        $this->onPlayerEnchantingOptionsRequest($session, $event);
    }
    protected function onPlayerEnchantingOptionsRequest(Session $session, PlayerEnchantingOptionsRequestEvent $event): void
    {
    }

    final public function handlePlayerEntityInteract(Session $session, PlayerEntityInteractEvent $event): void
    {
        $this->onPlayerEntityInteract($session, $event);
    }
    protected function onPlayerEntityInteract(Session $session, PlayerEntityInteractEvent $event): void
    {
    }

    final public function handlePlayerEntityPick(Session $session, PlayerEntityPickEvent $event): void
    {
        $this->onPlayerEntityPick($session, $event);
    }
    protected function onPlayerEntityPick(Session $session, PlayerEntityPickEvent $event): void
    {
    }

    final public function handlePlayerEvent(Session $session, PlayerEvent $event): void
    {
        $this->onPlayerEvent($session, $event);
    }
    protected function onPlayerEvent(Session $session, PlayerEvent $event): void
    {
    }

    final public function handlePlayerExhaust(Session $session, PlayerExhaustEvent $event): void
    {
        $this->onPlayerExhaust($session, $event);
    }
    protected function onPlayerExhaust(Session $session, PlayerExhaustEvent $event): void
    {
    }

    final public function handlePlayerExperienceChange(Session $session, PlayerExperienceChangeEvent $event): void
    {
        $this->onPlayerExperienceChange($session, $event);
    }
    protected function onPlayerExperienceChange(Session $session, PlayerExperienceChangeEvent $event): void
    {
    }

    final public function handlePlayerGameModeChange(Session $session, PlayerGameModeChangeEvent $event): void
    {
        $this->onPlayerGameModeChange($session, $event);
    }
    protected function onPlayerGameModeChange(Session $session, PlayerGameModeChangeEvent $event): void
    {
    }

    final public function handlePlayerInteract(Session $session, PlayerInteractEvent $event): void
    {
        $this->onPlayerInteract($session, $event);
    }
    protected function onPlayerInteract(Session $session, PlayerInteractEvent $event): void
    {
    }

    final public function handlePlayerItemConsume(Session $session, PlayerItemConsumeEvent $event): void
    {
        $this->onPlayerItemConsume($session, $event);
    }
    protected function onPlayerItemConsume(Session $session, PlayerItemConsumeEvent $event): void
    {
    }

    final public function handlePlayerItemEnchant(Session $session, PlayerItemEnchantEvent $event): void
    {
        $this->onPlayerItemEnchant($session, $event);
    }
    protected function onPlayerItemEnchant(Session $session, PlayerItemEnchantEvent $event): void
    {
    }

    final public function handlePlayerItemHeld(Session $session, PlayerItemHeldEvent $event): void
    {
        $this->onPlayerItemHeld($session, $event);
    }
    protected function onPlayerItemHeld(Session $session, PlayerItemHeldEvent $event): void
    {
    }

    final public function handlePlayerItemUse(Session $session, PlayerItemUseEvent $event): void
    {
        $this->onPlayerItemUse($session, $event);
    }
    protected function onPlayerItemUse(Session $session, PlayerItemUseEvent $event): void
    {
    }

    final public function handlePlayerJoin(Session $session, PlayerJoinEvent $event): void
    {
        $this->onPlayerJoin($session, $event);
    }
    protected function onPlayerJoin(Session $session, PlayerJoinEvent $event): void
    {
    }

    final public function handlePlayerJump(Session $session, PlayerJumpEvent $event): void
    {
        $this->onPlayerJump($session, $event);
    }
    protected function onPlayerJump(Session $session, PlayerJumpEvent $event): void
    {
    }

    final public function handlePlayerKick(Session $session, PlayerKickEvent $event): void
    {
        $this->onPlayerKick($session, $event);
    }
    protected function onPlayerKick(Session $session, PlayerKickEvent $event): void
    {
    }

    final public function handlePlayerLogin(Session $session, PlayerLoginEvent $event): void
    {
        $this->onPlayerLogin($session, $event);
    }
    protected function onPlayerLogin(Session $session, PlayerLoginEvent $event): void
    {
    }

    final public function handlePlayerMissSwing(Session $session, PlayerMissSwingEvent $event): void
    {
        $this->onPlayerMissSwing($session, $event);
    }
    protected function onPlayerMissSwing(Session $session, PlayerMissSwingEvent $event): void
    {
    }

    final public function handlePlayerMove(Session $session, PlayerMoveEvent $event): void
    {
        $this->onPlayerMove($session, $event);
    }
    protected function onPlayerMove(Session $session, PlayerMoveEvent $event): void
    {
    }

    final public function handlePlayerPostChunkSend(Session $session, PlayerPostChunkSendEvent $event): void
    {
        $this->onPlayerPostChunkSend($session, $event);
    }
    protected function onPlayerPostChunkSend(Session $session, PlayerPostChunkSendEvent $event): void
    {
    }

    final public function handlePlayerPreLogin(Session $session, PlayerPreLoginEvent $event): void
    {
        $this->onPlayerPreLogin($session, $event);
    }
    protected function onPlayerPreLogin(Session $session, PlayerPreLoginEvent $event): void
    {
    }

    final public function handlePlayerQuit(Session $session, PlayerQuitEvent $event): void
    {
        $this->onPlayerQuit($session, $event);
    }
    protected function onPlayerQuit(Session $session, PlayerQuitEvent $event): void
    {
    }

    final public function handlePlayerResourcePackOffer(Session $session, PlayerResourcePackOfferEvent $event): void
    {
        $this->onPlayerResourcePackOffer($session, $event);
    }
    protected function onPlayerResourcePackOffer(Session $session, PlayerResourcePackOfferEvent $event): void
    {
    }

    final public function handlePlayerRespawnAnchorUse(Session $session, PlayerRespawnAnchorUseEvent $event): void
    {
        $this->onPlayerRespawnAnchorUse($session, $event);
    }
    protected function onPlayerRespawnAnchorUse(Session $session, PlayerRespawnAnchorUseEvent $event): void
    {
    }

    final public function handlePlayerRespawn(Session $session, PlayerRespawnEvent $event): void
    {
        $this->onPlayerRespawn($session, $event);
    }
    protected function onPlayerRespawn(Session $session, PlayerRespawnEvent $event): void
    {
    }

    final public function handlePlayerToggleFlight(Session $session, PlayerToggleFlightEvent $event): void
    {
        $this->onPlayerToggleFlight($session, $event);
    }
    protected function onPlayerToggleFlight(Session $session, PlayerToggleFlightEvent $event): void
    {
    }

    final public function handlePlayerToggleGlide(Session $session, PlayerToggleGlideEvent $event): void
    {
        $this->onPlayerToggleGlide($session, $event);
    }
    protected function onPlayerToggleGlide(Session $session, PlayerToggleGlideEvent $event): void
    {
    }

    final public function handlePlayerToggleSneak(Session $session, PlayerToggleSneakEvent $event): void
    {
        $this->onPlayerToggleSneak($session, $event);
    }
    protected function onPlayerToggleSneak(Session $session, PlayerToggleSneakEvent $event): void
    {
    }

    final public function handlePlayerToggleSprint(Session $session, PlayerToggleSprintEvent $event): void
    {
        $this->onPlayerToggleSprint($session, $event);
    }
    protected function onPlayerToggleSprint(Session $session, PlayerToggleSprintEvent $event): void
    {
    }

    final public function handlePlayerToggleSwim(Session $session, PlayerToggleSwimEvent $event): void
    {
        $this->onPlayerToggleSwim($session, $event);
    }
    protected function onPlayerToggleSwim(Session $session, PlayerToggleSwimEvent $event): void
    {
    }

    final public function handlePlayerTransfer(Session $session, PlayerTransferEvent $event): void
    {
        $this->onPlayerTransfer($session, $event);
    }
    protected function onPlayerTransfer(Session $session, PlayerTransferEvent $event): void
    {
    }

    final public function handlePlayerViewDistanceChange(Session $session, PlayerViewDistanceChangeEvent $event): void
    {
        $this->onPlayerViewDistanceChange($session, $event);
    }
    protected function onPlayerViewDistanceChange(Session $session, PlayerViewDistanceChangeEvent $event): void
    {
    }


    // ==========================================
    // Plugin Events
    // ==========================================
    final public function handlePluginDisable(Session $session, PluginDisableEvent $event): void
    {
        $this->onPluginDisable($session, $event);
    }
    protected function onPluginDisable(Session $session, PluginDisableEvent $event): void
    {
    }

    final public function handlePluginEnable(Session $session, PluginEnableEvent $event): void
    {
        $this->onPluginEnable($session, $event);
    }
    protected function onPluginEnable(Session $session, PluginEnableEvent $event): void
    {
    }

    final public function handlePluginEvent(Session $session, PluginEvent $event): void
    {
        $this->onPluginEvent($session, $event);
    }
    protected function onPluginEvent(Session $session, PluginEvent $event): void
    {
    }


    // ==========================================
    // Server Events
    // ==========================================
    final public function handleCommand(Session $session, CommandEvent $event): void
    {
        $this->onCommand($session, $event);
    }
    protected function onCommand(Session $session, CommandEvent $event): void
    {
    }

    final public function handleDataPacketDecode(Session $session, DataPacketDecodeEvent $event): void
    {
        $this->onDataPacketDecode($session, $event);
    }
    protected function onDataPacketDecode(Session $session, DataPacketDecodeEvent $event): void
    {
    }

    final public function handleDataPacketReceive(Session $session, DataPacketReceiveEvent $event): void
    {
        $this->onDataPacketReceive($session, $event);
    }
    protected function onDataPacketReceive(Session $session, DataPacketReceiveEvent $event): void
    {
    }

    final public function handleDataPacketSend(Session $session, DataPacketSendEvent $event): void
    {
        $this->onDataPacketSend($session, $event);
    }
    protected function onDataPacketSend(Session $session, DataPacketSendEvent $event): void
    {
    }

    final public function handleLowMemory(Session $session, LowMemoryEvent $event): void
    {
        $this->onLowMemory($session, $event);
    }
    protected function onLowMemory(Session $session, LowMemoryEvent $event): void
    {
    }

    final public function handleNetworkInterface(Session $session, NetworkInterfaceEvent $event): void
    {
        $this->onNetworkInterface($session, $event);
    }
    protected function onNetworkInterface(Session $session, NetworkInterfaceEvent $event): void
    {
    }

    final public function handleNetworkInterfaceRegister(Session $session, NetworkInterfaceRegisterEvent $event): void
    {
        $this->onNetworkInterfaceRegister($session, $event);
    }
    protected function onNetworkInterfaceRegister(Session $session, NetworkInterfaceRegisterEvent $event): void
    {
    }

    final public function handleNetworkInterfaceUnregister(Session $session, NetworkInterfaceUnregisterEvent $event): void
    {
        $this->onNetworkInterfaceUnregister($session, $event);
    }
    protected function onNetworkInterfaceUnregister(Session $session, NetworkInterfaceUnregisterEvent $event): void
    {
    }

    final public function handleQueryRegenerate(Session $session, QueryRegenerateEvent $event): void
    {
        $this->onQueryRegenerate($session, $event);
    }
    protected function onQueryRegenerate(Session $session, QueryRegenerateEvent $event): void
    {
    }

    final public function handleServerEvent(Session $session, ServerEvent $event): void
    {
        $this->onServerEvent($session, $event);
    }
    protected function onServerEvent(Session $session, ServerEvent $event): void
    {
    }

    final public function handleUpdateNotify(Session $session, UpdateNotifyEvent $event): void
    {
        $this->onUpdateNotify($session, $event);
    }
    protected function onUpdateNotify(Session $session, UpdateNotifyEvent $event): void
    {
    }


    // ==========================================
    // World Events
    // ==========================================
    final public function handleChunkEvent(Session $session, ChunkEvent $event): void
    {
        $this->onChunkEvent($session, $event);
    }
    protected function onChunkEvent(Session $session, ChunkEvent $event): void
    {
    }

    final public function handleChunkLoad(Session $session, ChunkLoadEvent $event): void
    {
        $this->onChunkLoad($session, $event);
    }
    protected function onChunkLoad(Session $session, ChunkLoadEvent $event): void
    {
    }

    final public function handleChunkPopulate(Session $session, ChunkPopulateEvent $event): void
    {
        $this->onChunkPopulate($session, $event);
    }
    protected function onChunkPopulate(Session $session, ChunkPopulateEvent $event): void
    {
    }

    final public function handleChunkUnload(Session $session, ChunkUnloadEvent $event): void
    {
        $this->onChunkUnload($session, $event);
    }
    protected function onChunkUnload(Session $session, ChunkUnloadEvent $event): void
    {
    }

    final public function handleSpawnChange(Session $session, SpawnChangeEvent $event): void
    {
        $this->onSpawnChange($session, $event);
    }
    protected function onSpawnChange(Session $session, SpawnChangeEvent $event): void
    {
    }

    final public function handleWorldDifficultyChange(Session $session, WorldDifficultyChangeEvent $event): void
    {
        $this->onWorldDifficultyChange($session, $event);
    }
    protected function onWorldDifficultyChange(Session $session, WorldDifficultyChangeEvent $event): void
    {
    }

    final public function handleWorldDisplayNameChange(Session $session, WorldDisplayNameChangeEvent $event): void
    {
        $this->onWorldDisplayNameChange($session, $event);
    }
    protected function onWorldDisplayNameChange(Session $session, WorldDisplayNameChangeEvent $event): void
    {
    }

    final public function handleWorldEvent(Session $session, WorldEvent $event): void
    {
        $this->onWorldEvent($session, $event);
    }
    protected function onWorldEvent(Session $session, WorldEvent $event): void
    {
    }

    final public function handleWorldInit(Session $session, WorldInitEvent $event): void
    {
        $this->onWorldInit($session, $event);
    }
    protected function onWorldInit(Session $session, WorldInitEvent $event): void
    {
    }

    final public function handleWorldLoad(Session $session, WorldLoadEvent $event): void
    {
        $this->onWorldLoad($session, $event);
    }
    protected function onWorldLoad(Session $session, WorldLoadEvent $event): void
    {
    }

    final public function handleWorldParticle(Session $session, WorldParticleEvent $event): void
    {
        $this->onWorldParticle($session, $event);
    }
    protected function onWorldParticle(Session $session, WorldParticleEvent $event): void
    {
    }

    final public function handleWorldSave(Session $session, WorldSaveEvent $event): void
    {
        $this->onWorldSave($session, $event);
    }
    protected function onWorldSave(Session $session, WorldSaveEvent $event): void
    {
    }

    final public function handleWorldSound(Session $session, WorldSoundEvent $event): void
    {
        $this->onWorldSound($session, $event);
    }
    protected function onWorldSound(Session $session, WorldSoundEvent $event): void
    {
    }

    final public function handleWorldUnload(Session $session, WorldUnloadEvent $event): void
    {
        $this->onWorldUnload($session, $event);
    }
    protected function onWorldUnload(Session $session, WorldUnloadEvent $event): void
    {
    }
}