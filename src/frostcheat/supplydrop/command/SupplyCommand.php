<?php

namespace frostcheat\supplydrop\command;

use frostcheat\supplydrop\Main;
use frostcheat\supplydrop\backup\ItemsBackup;
use frostcheat\supplydrop\task\SupplyDropTask;
use frostcheat\supplydrop\Utils;

use frostcheat\supplydrop\modules\SupplyModule;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as TE;
use pocketmine\player\Player;
use pocketmine\block\VanillaBlocks;
use pocketmine\nbt\tag\StringTag;

class SupplyCommand extends Command
{

    /**
     * SupplyCommand constructor.
     */
    public function __construct()
    {
        parent::__construct("supplydrop");
        $this->setPermission("supplydrop.command");
    }

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     */
    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (count($args) === 0) {
            $sender->sendMessage(TE::RED . "/supplydrop help");
            return;
        }
        switch ($args[0]) {
            case "edit":
                if (!$sender->getServer()->isOp($sender->getName())) {
                    $sender->sendMessage(TE::RED . "You don't have permissions");
                    return;
                }
                if (!$sender instanceof Player) {
                    $sender->sendMessage(TE::RED . "This message can only be executed in game!");
                    return;
                }
                $player = Main::getInstance()->getServer()->getPlayerByPrefix($sender->getName());
                Main::$rewards = new SupplyModule($player->getInventory()->getContents());
                $sender->sendMessage(TE::GREEN . "Items Edited Sucesfully");
                
                break;
                
            case "spawn":
                Main::getInstance()->getScheduler()->scheduleRepeatingTask(new SupplyDropTask(Main::getInstance()),1);
                break;
        }
    }
}