<?php

namespace frostcheat\supplydrops\utils;

use frostcheat\supplydrops\Loader;
use frostcheat\supplydrops\supply\SupplyDropManager;

use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\world\WorldException;

class Utils {
    use SingletonTrait;

    
    public function spawnSupplyDrop(World $world): void {
        $config = Loader::getInstance()->getConfig();

        $rangeX = abs((int)$config->get("coord-x", 100));
        $rangeZ = abs((int)$config->get("coord-z", 100));
        $x = mt_rand(-$rangeX, $rangeX);
        $z = mt_rand(-$rangeZ, $rangeZ);

        $minItems = max(1, (int)$config->get("min-items", 1));
        $maxItems = min(27, (int)$config->get("max-items", 27), count(SupplyDropManager::getInstance()->getItems()));
        if ($minItems > $maxItems) {
            $minItems = $maxItems;
        }

        $chunkX = $x >> 4;
        $chunkZ = $z >> 4;

        if ($world->loadChunk($chunkX, $chunkZ) === null) {
            $world->orderChunkPopulation($chunkX, $chunkZ, new ChunkLoader())->onCompletion(
                fn() => $this->handleSpawn($world, $x, $z, $minItems, $maxItems),
                fn(\Throwable $e) => Loader::getInstance()->getLogger()->error("Chunk generation failed at X:$x Z:$z: " . $e->getMessage())
            );
        } else {
            $this->handleSpawn($world, $x, $z, $minItems, $maxItems);
        }
    }

    private function handleSpawn(World $world, int $x, int $z, int $minItems, int $maxItems): void {
        try {
            $y = $world->getHighestBlockAt($x, $z);
        } catch (WorldException $e) {
            Loader::getInstance()->getLogger()->warning("Failed to get highest block at X:$x Z:$z - " . $e->getMessage());
            return;
        }

        $pos = new Position($x, $y + 1, $z, $world);
        if (!$world->getBlock($pos)->isTransparent()) {
            $pos = $pos->add(0, 1, 0);
        }

        $allItems = SupplyDropManager::getInstance()->getItems();
        if (empty($allItems)) {
            Loader::getInstance()->getLogger()->warning("No supply items available.");
            return;
        }

        shuffle($allItems);
        $amount = mt_rand($minItems, min($maxItems, count($allItems)));
        $itemsToUse = array_slice($allItems, 0, $amount);

        $world->setBlock($pos, VanillaBlocks::CHEST());

        $tile = $world->getTile($pos);
        if (!$tile instanceof Chest) {
            $tile = new Chest($world, $pos);
            $world->addTile($tile);
        }

        foreach ($itemsToUse as $item) {
            $tile->getInventory()->addItem($item);
        }

        Loader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize(
            "&bSupplyDrop spawned at &eX:$x Y:$y Z:$z &bin world &e{$world->getDisplayName()} &bwith &e$amount &bitem(s)!"
        ));
    }


    public function strToTime(string $input): int {
        $units = [
            's' => 1,
            'm' => 60,
            'h' => 3600,
            'd' => 86400,
            'w' => 604800,
            'mo' => 2592000,
            'y' => 31536000
        ];

        if (preg_match('/^(\d+)(mo|[smhdwy])$/', strtolower($input), $matches)) {
            $value = (int)$matches[1];
            $unit = $matches[2];

            if (isset($units[$unit])) {
                return $value * $units[$unit];
            }
        }
        return 0;
    }
}