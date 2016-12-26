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
use pocketmine\utils\BinaryStream;

/**
 * @since API 3.0.0
 */
class FloatDataProperty extends DataProperty{

	const DATA_TYPE = EntityDataManager::DATA_TYPE_FLOAT;

	/** @var float */
	protected $value;

	public function readFrom(BinaryStream $stream){
		$this->setValue($stream->getLFloat());
	}

	public function writeTo(BinaryStream $stream){
		$stream->putLFloat($this->value);
	}

	public function equals(DataProperty $property) : bool{
		return $property instanceof FloatDataProperty and $property->getValue() === $this->value;
	}

	/**
	 * @return float
	 */
	public function getValue(){
		return $this->value;
	}

	public function setValue($value){
		if(is_float($value) or is_int($value)){
			$this->value = (float) $value;
		}else{
			throw new \InvalidArgumentException("Expected a floating point value, got " . gettype($value));
		}
	}
}