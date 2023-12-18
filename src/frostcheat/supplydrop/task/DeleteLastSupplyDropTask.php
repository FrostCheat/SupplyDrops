<?php

namespace frostcheat\supplydrop\task;

use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\Task;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

use frostcheat\supplydrop\Main;

class DeleteLastSupplyDropTask extends Task {
    private $plugin;
    private $position;

    public function __construct(Main $plugin, Vector3 $position){
        $this->plugin = $plugin; 
        $this->position = $position;
    }

    public function onRun(): void {
        $this->plugin->getServer()->getWorldManager()->getDefaultWorld()->setBlock($this->position, VanillaBlocks::AIR());
        $this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&cEl supplydrop anterior ha sido eliminado"));
        $this->getHandler()->cancel();
    }
}