<?php

namespace frostcheat\supplydrop;

use frostcheat\supplydrop\task\GenerateChunkTask;
use pocketmine\world\World;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkLoader as PMChunkLoader;

class ChunkLoader extends PMChunkLoader {

    private $world;

    public function __construct(World $world) {
        $this->world = $world;
    }

    public function generateChunk(int $x, int $z){
        $chunkX = $x >> 4;
        $chunkZ = $z >> 4;
        
        $this->world->loadChunk($chunkX, $chunkZ);
        $this->world->chunkHash($chunkX, $chunkZ);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new GenerateChunkTask($this->world, $chunkX, $chunkZ), 1);
    }

}