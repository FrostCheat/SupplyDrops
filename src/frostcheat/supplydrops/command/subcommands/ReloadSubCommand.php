<?php

namespace frostcheat\supplydrops\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\supplydrops\Loader;
use frostcheat\supplydrops\provider\Provider;
use frostcheat\supplydrops\supply\SupplyDropManager;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ReloadSubCommand extends BaseSubCommand {
    public function __construct() {
        parent::__construct("reload", "Reloads the plugin config");
        $this->setPermission("supplydrops.command.reload");
    }

    public function prepare(): void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        Loader::getInstance()->reloadConfig();
        Provider::getInstance()->load();
        SupplyDropManager::getInstance()->load();
        $sender->sendMessage(TextFormat::colorize("&aThe plugin configuration has been reloaded successfully."));
    }
}