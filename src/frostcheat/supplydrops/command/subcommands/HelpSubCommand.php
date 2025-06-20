<?php

namespace frostcheat\supplydrops\command\subcommands;

use CortexPE\Commando\BaseSubCommand;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class HelpSubCommand extends BaseSubCommand
{
    public function __construct(private array $subCommands)
    {
        parent::__construct('help', 'Help commands Item Editor');
        $this->setPermission('itemeditor.command.help');
    }

    protected function prepare(): void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        foreach ($this->subCommands as $subCommand) {
            $sender->sendMessage(TextFormat::colorize("&b/supplydrops " . $subCommand->getName() . " &f- &7" . $subCommand->getDescription()));
        }
    }
}