<?php

namespace frostcheat\supplydrop\task;

use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\Task;
use pocketmine\world\World;

class GenerateChunkTask extends Task {
    private $world;
    private $chunkX;
    private $chunkZ;

    public function __construct(World $world, int $chunkX, int $chunkZ) {
        $this->world = $world; 
        $this->chunkX = $chunkX;
        $this->chunkZ = $chunkZ;
    }

    public function onRun(): void {
        $this->world->loadChunk($this->chunkX, $this->chunkZ);
        $this->world->setBlockAt($this->chunkX * 16, 0, $this->chunkZ * 16, VanillaBlocks::STONE()); 
        $this->getHandler()->cancel();
    }
}