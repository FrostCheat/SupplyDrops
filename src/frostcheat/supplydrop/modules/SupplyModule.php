<?php

namespace frostcheat\supplydrop\modules;

use frostcheat\supplydrop\Main;
use frostcheat\supplydrop\Utils;

use pocketmine\utils\Config;
use pocketmine\item\Item;

/**
 * Class SupplyModule
 * @package frostcheat\supplydrop\modules
 */
class SupplyModule
{

    /** @var array|null */
    public static $rewards = [];

    /**
     * SupplyModule constructor.
     * @param array|null $rewards
     */
    public function __construct(?array $rewards = [])
    {
        self::$rewards = $rewards;
        $rewardData = [];
        $file = new Config(Main::getInstance()->getDataFolder() . "backup" . DIRECTORY_SEPARATOR . "rewards.yml", Config::YAML);
        foreach (self::getItems() as $slot => $reward) {
            $rewardData[$slot] = Utils::itemSerialize($reward);
        }
        $file->set("items", $rewardData);
        $file->save();
    }

    /**
     * @return array
     */
    public static function getItems(): array
    {
        return self::$rewards;
    }

    public static function getRandomItems(int $min = 5, int $max = 15): array 
    {
        $count = mt_rand($min, $max);
        $items = [];

        if(empty(self::$rewards)){
            return []; 
        }
        
        while(count($items) < $count) {
            $randomItem = self::$rewards[array_rand(self::$rewards)];
            $items[] = $randomItem;
        }
        
        return $items;
    }
}