<?php

namespace frostcheat\supplydrops\provider;

use frostcheat\supplydrops\supply\SupplyDropManager;
use pocketmine\item\Item;

use frostcheat\supplydrops\Loader;
use frostcheat\supplydrops\utils\Serialize;
use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Provider {
    use SingletonTrait;

    private Config $configItems;

    public function load(): void {
        $this->configItems = new Config(Loader::getInstance()->getDataFolder() . "items.yml", Config::YAML);
    }

    /**
     * @return Item[]
     */
    public function getItems(): array {
        $items = [];

        foreach ($this->configItems->getAll() as $item) {
            $items[] = Serialize::deserialize( $item);
        }

        return $items;
    }

    public function saveItems(): void {
        $serialized = [];

        foreach (SupplyDropManager::getInstance()->getItems() as $item) {
           $serialized[] = Serialize::serialize($item);
        }

        $path = Loader::getInstance()->getDataFolder() . "items.yml";

        Loader::getInstance()->getServer()->getAsyncPool()->submitTask(new class($path, $serialized) extends AsyncTask {

            private string $path;
            private string $data;

            public function __construct(string $path, array $data) {
               $this->path = $path;
               $this->data = serialize($data);
            }

            public function onRun(): void {
              $data = unserialize($this->data);
              if (!is_array($data)) return;

              yaml_emit_file($this->path, $data);
            }
        });
    }
}