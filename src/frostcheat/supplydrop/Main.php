<?php

namespace frostcheat\supplydrop;

use pocketmine\plugin\PluginBase;
use frostcheat\supplydrop\command\SupplyCommand;
use muqsit\invmenu\InvMenuHandler;
use frostcheat\supplydrop\backup\ItemsBackup;
use frostcheat\supplydrop\task\SupplyDropTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;
    
    public static $rewards;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onEnable(): void {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $this->getServer()->getCommandMap()->register("hcf", new SupplyCommand());
        $this->saveDefaultConfig();
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $this->getScheduler()->scheduleRepeatingTask(new SupplyDropTask($this), Main::getInstance()->getConfig()->get("time"));
        }), Main::getInstance()->getConfig()->get("time"));

        if(!is_dir($this->getDataFolder()."backup")){
        	@mkdir($this->getDataFolder()."backup");
        }

        ItemsBackup::init();
    }

    public function onDisable(): void {
        ItemsBackup::save();
    }

    public static function getInstance(): Main {
        return self::$instance;
    }
}