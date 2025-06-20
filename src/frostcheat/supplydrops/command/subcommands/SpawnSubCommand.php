<?php

namespace frostcheat\supplydrops\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\supplydrops\supply\SupplyDropManager;
use frostcheat\supplydrops\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SpawnSubCommand extends BaseSubCommand {
    public function __construct() {
        parent::__construct("spawn", "Spawn a supplydrop");
        $this->setPermission("supplydrops.command.spawn");
    }

    public function prepare(): void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        foreach (SupplyDropManager::getInstance()->worlds as $world) {
            Utils::getInstance()->spawnSupplyDrop($world);
        }

        $sender->sendMessage(TextFormat::colorize("&aSupply drops have been generated successfully"));
    }
}