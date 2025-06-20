<?php

namespace frostcheat\supplydrops\supply;

use frostcheat\supplydrops\Loader;
use frostcheat\supplydrops\provider\Provider;
use pocketmine\item\Item;
use pocketmine\utils\SingletonTrait;

class SupplyDropManager {
    use SingletonTrait;

    /**
     * @return Item[]
     */
    private array $items = [];

    public array $worlds = [];

    public function load(): void {
        Loader::getInstance()->reloadConfig();
        $this->items = Provider::getInstance()->getItems();

        foreach (Loader::getInstance()->getConfig()->get("worlds", []) as $worldName) {
            $world = Loader::getInstance()->getServer()->getWorldManager()->getWorldByName($worldName);
            if ($world !== null) {
                $this->worlds[] = $world;
            }
        }
    }

    public function getItems(): array {
        return $this->items;
    }

    public function setItems(array $items): void {
        $this->items = $items;
        Provider::getInstance()->saveItems();
    }
}