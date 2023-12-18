<?php

namespace frostcheat\supplydrop;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TE;
use pocketmine\data\bedrock\item\ItemTypeDeserializeException;
use pocketmine\data\SavedDataLoadingException;

use pocketmine\item\Durable;
use pocketmine\item\Item;

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

use pocketmine\world\format\io\GlobalItemDataHandlers;

/**
 * Class Utils
 * @package SupplyDrops
 */
class Utils
{

    /**
     * @param $config
     * @return bool|mixed
     */
    public static function getConfiguration($config)
    {
        return Main::getInstance()->getConfig()->get($config);
    }

    /**
     * @param Item $reward
     * @return array
     */
    public static function itemSerialize(Item $item): array{
        $serialized = GlobalItemDataHandlers::getSerializer()->serializeType($item);
		$data = [
			"id" => $serialized->getName()
		];
		if($item->getCount() !== 1){
			$data["count"] = $item->getCount();
		}
        //if($item instanceof Durable){
        if($serialized->getMeta() !== 0){
            $data["damage"] = $item instanceof Durable ? $item->getDamage() : $serialized->getMeta();
        }
       // }
		if($item->hasNamedTag()){
			$data["nbt_b64"] = base64_encode((new LittleEndianNbtSerializer())->write(new TreeRoot($item->getNamedTag())));
		}
		return $data;
	}

    /**
     * @param array $rewards
     * @return Item
     */
    public static function itemDeserialize(array $data): Item{
        $nbt = "";

        //Backwards compatibility
        if (isset($data["nbt"])) {
            $nbt = $data["nbt"];
        } elseif (isset($data["nbt_hex"])) {
            $nbt = hex2bin($data["nbt_hex"]);
        } elseif (isset($data["nbt_b64"])) {
            $nbt = base64_decode($data["nbt_b64"], true);
        }
        $itemStackData = GlobalItemDataHandlers::getUpgrader()->upgradeItemTypeDataString($data['id'], $data['damage'] ?? 0, $data['count'] ?? 1,
            $nbt !== "" ? (new LittleEndianNbtSerializer())->read($nbt)->mustGetCompoundTag() : null
        );

        try{
            return GlobalItemDataHandlers::getDeserializer()->deserializeStack($itemStackData);
        }catch(ItemTypeDeserializeException $e){
            throw new SavedDataLoadingException($e->getMessage(), 0, $e);
        }
	}
}