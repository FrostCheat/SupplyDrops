<?php

namespace frostcheat\supplydrops;

use CortexPE\Commando\PacketHooker;

use frostcheat\supplydrops\command\SupplyDropCommand;
use frostcheat\supplydrops\provider\Provider;
use frostcheat\supplydrops\supply\SupplyDropManager;
use frostcheat\supplydrops\utils\Utils;
use JackMD\ConfigUpdater\ConfigUpdater;
use JackMD\UpdateNotifier\UpdateNotifier;

use muqsit\invmenu\InvMenuHandler;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase {
    use SingletonTrait;

    private const CONFIG_VERSION = 1;

    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        Provider::getInstance()->load();
        SupplyDropManager::getInstance()->load();
        
        UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
        if (ConfigUpdater::checkUpdate($this, $this->getConfig(), "config-version", self::CONFIG_VERSION)) {
            $this->reloadConfig();
        }

        if (!PacketHooker::isRegistered())
            PacketHooker::register($this);

        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);

        $time = Utils::getInstance()->strToTime($this->getConfig()->get("time", "-1"));
        if ($time !== 0) {
            $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
                foreach (SupplyDropManager::getInstance()->worlds as $world) {
                    Utils::getInstance()->spawnSupplyDrop($world);
                }
            }), Utils::getInstance()->strToTime($this->getConfig()->get("time", "1h")) * 20);
        }

        $this->saveDefaultConfig();

        $this->getServer()->getCommandMap()->register("supplydrops", new SupplyDropCommand($this));
    }

    public function onDisable(): void {
        Provider::getInstance()->saveItems();
    }
}