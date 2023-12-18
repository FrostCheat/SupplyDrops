<?php

namespace frostcheat\supplydrop\backup;


use frostcheat\supplydrop\Main;
use frostcheat\supplydrop\Utils;
use frostcheat\supplydrop\modules\SupplyModule;
use pocketmine\utils\Config;

class ItemsBackup {

    public static function init() : void {
        $rewards = [];

        $data = new Config(Main::getInstance()->getDataFolder()."backup".DIRECTORY_SEPARATOR."rewards.yml", Config::YAML);
        foreach($data->getAll() as $rewardBackup){
            $result = $data->getAll();
            if(isset($result["items"])){
                foreach($result["items"] as $number => $reward){
                    $rewards[$number] = Utils::itemDeserialize($reward);
                    Main::$rewards = new SupplyModule($rewards);
                }
            }
        }
    }

    public static function save() : void {
        $rewardData = [];
        $result = new Config(Main::getInstance()->getDataFolder()."backup".DIRECTORY_SEPARATOR."rewards.yml", Config::YAML);
            foreach(SupplyModule::getItems() as $number => $reward){
                $rewardData[$number] = Utils::itemSerialize($reward);
            }
        $result->set("items", $rewardData);
        $result->save();
    }

}

?>