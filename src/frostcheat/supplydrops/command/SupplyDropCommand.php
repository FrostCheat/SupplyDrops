<?php

namespace frostcheat\supplydrops\command;

use CortexPE\Commando\BaseCommand;
use frostcheat\supplydrops\command\subcommands\EditSubCommand;
use frostcheat\supplydrops\command\subcommands\HelpSubCommand;
use frostcheat\supplydrops\command\subcommands\ReloadSubCommand;
use frostcheat\supplydrops\command\subcommands\SpawnSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class SupplyDropCommand extends BaseCommand {

    public function __construct(Plugin $plugin) {
        parent::__construct($plugin, "supplydrops", "SupplyDrop Main Command", ["supply", "sdrop"]);
        $this->setPermission("supplydrops.command");
    }

    public function prepare(): void {
        $this->registerSubCommand(new EditSubCommand());
        $this->registerSubCommand(new ReloadSubCommand());
        $this->registerSubCommand(new SpawnSubCommand());
        $this->registerSubCommand(new HelpSubCommand($this->getSubCommands()));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage(TextFormat::colorize("&cUse /$aliasUsed help"));
    }
}