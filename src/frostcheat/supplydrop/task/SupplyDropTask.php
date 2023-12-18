<?php

namespace frostcheat\supplydrop\task;

use pocketmine\scheduler\Task;
use frostcheat\supplydrop\modules\SupplyModule;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\block\VanillaBlocks;
use frostcheat\supplydrop\Main;
use pocketmine\world\WorldException;
use frostcheat\supplydrop\ChunkLoader;
use pocketmine\world\format\io\exception\CorruptedChunkException;

class SupplyDropTask extends Task {
    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin; 
    }

    public function onRun(): void {
        
        $x = mt_rand(Main::getInstance()->getConfig()->get("coord-x"), Main::getInstance()->getConfig()->get("coord-z")); 
        $z = mt_rand(Main::getInstance()->getConfig()->get("coord-x"), Main::getInstance()->getConfig()->get("coord-z"));

        $y = 80;
        
        $pos = new Vector3($x, $y + 1, $z);

        $min = Main::getInstance()->getConfig()->get("min-items");
        $max = Main::getInstance()->getConfig()->get("max-items");

        if ($min < 1 || $min > 27) {
            $min = 1;
        }

        if ($max > 27 || $max < 1) {
            $max = 27;
        }
        
        $loot = SupplyModule::getRandomItems((int) $min, (int) $max); 

        $loot = SupplyModule::getRandomItems((int) $min, (int) $max);
        
        if(count($loot) < $min){
            $this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&cEl SupplyDrop no se pudo colocar porque la cantidad de items que hay es menor a la cantidad de items minima definida!!!"));
            $this->getHandler()->cancel();
            return;
        }
         
        $chest = VanillaBlocks::CHEST();
        try {
            $this->plugin->getInstance()->getServer()->getWorldManager()->getDefaultWorld()->setBlock($pos, $chest);
        } catch (WorldException $e) {
            $this->plugin->getLogger()->critical($e->getMessage());
            $this->getHandler()->cancel();
            return;
        }

        $chesttile = $this->plugin->getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getTile($pos);
        
        foreach($loot as $item){
            $chesttile->getInventory()->addItem($item);
        } 
        $this->plugin->getServer()->broadcastMessage(TextFormat::colorize("&bSupplydrop colocado en: &f".$x.", ".$y.", ".$z));
        
        $this->plugin->getInstance()->getServer()->getWorldManager()->getDefaultWorld()->addTile($chesttile);
        Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new DeleteLastSupplyDropTask(Main::getInstance(), $pos), Main::getInstance()->getConfig()->get("time"), 1);
        $this->getHandler()->cancel();
   }
}