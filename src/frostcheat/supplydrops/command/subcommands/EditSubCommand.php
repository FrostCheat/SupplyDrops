<?php

namespace frostcheat\supplydrops\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\supplydrops\supply\SupplyDropManager;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditSubCommand extends BaseSubCommand {
    public function __construct() {
        parent::__construct("edit", "Edit supplydrop content");
        $this->setPermission("supplydrops.command.edit");
    }

    public function prepare(): void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize("&cYou must be a player to execute this command."));
            return;
        }

        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        foreach (SupplyDropManager::getInstance()->getItems() as $item) {
            $menu->getInventory()->addItem($item);
        }

        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            SupplyDropManager::getInstance()->setItems(array_values($inventory->getContents()));

            $player->sendMessage(TextFormat::colorize("&aSupplyDrop items have been edited successfully"));
        });

        $menu->send($sender);
    }
}