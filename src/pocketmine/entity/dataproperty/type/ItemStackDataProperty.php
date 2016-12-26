<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types = 1);

namespace pocketmine\entity\dataproperty\type;

use pocketmine\entity\dataproperty\EntityDataManager;
use pocketmine\item\Item;
use pocketmine\utils\BinaryStream;

/**
 * @since API 3.0.0
 */
class ItemStackDataProperty extends DataProperty{

	const DATA_TYPE = EntityDataManager::DATA_TYPE_ITEMSTACK;

	/** @var Item */
	protected $value;

	public function readFrom(BinaryStream $stream){
		$this->setValue($stream->getSlot());
	}

	public function writeTo(BinaryStream $stream){
		$stream->putSlot($this->value);
	}

	public function equals(DataProperty $property) : bool{
		return $property instanceof ItemStackDataProperty and $property->getValue()->equals($this->value);
	}

	/**
	 * @return Item
	 */
	public function getValue(){
		return clone $this->value;
	}

	public function setValue($value){
		if($value instanceof Item){
			$this->value = clone $value;
		}else{
			throw new \InvalidArgumentException("Expected an Item object value, got " . gettype($value));
		}
	}

	public function __clone(){
		$this->value = clone $this->value;
	}
}