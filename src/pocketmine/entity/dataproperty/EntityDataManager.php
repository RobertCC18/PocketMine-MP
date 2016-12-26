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

namespace pocketmine\entity\dataproperty;

use pocketmine\entity\dataproperty\{
	BlockPosDataProperty,
	ByteDataProperty,
	FloatDataProperty,
	IntDataProperty,
	ItemStackDataProperty,
	LongDataProperty,
	ShortDataProperty,
	StringDataProperty,
	Vector3fDataProperty
};
use pocketmine\utils\BinaryStream;

/**
 * Manages synchronized entity runtime data properties
 * @since API 3.0.0
 */
class EntityDataManager{

	const DATA_TYPE_BYTE = 0;
	const DATA_TYPE_SHORT = 1;
	const DATA_TYPE_INT = 2;
	const DATA_TYPE_FLOAT = 3;
	const DATA_TYPE_STRING = 4;
	const DATA_TYPE_ITEMSTACK = 5;
	const DATA_TYPE_BLOCKPOS = 6;
	const DATA_TYPE_LONG = 7;
	const DATA_TYPE_VECTOR3F = 8;

	private static $knownTypes = [
		self::DATA_TYPE_BYTE      => ByteDataProperty::class,
		self::DATA_TYPE_SHORT     => ShortDataProperty::class,
		self::DATA_TYPE_INT       => IntDataProperty::class,
		self::DATA_TYPE_FLOAT     => FloatDataProperty::class,
		self::DATA_TYPE_STRING    => StringDataProperty::class,
		self::DATA_TYPE_ITEMSTACK => ItemStackDataProperty::class,
		self::DATA_TYPE_BLOCKPOS  => BlockPosDataProperty::class,
		self::DATA_TYPE_LONG      => LongDataProperty::class,
		self::DATA_TYPE_VECTOR3F  => Vector3fDataProperty::class
	];

	private static $index = [
		//TODO
	];

	/** @var DataProperty */
	private $dataProperties = [];

	/** @var DataProperty */
	private $updateProperties = []; //This is probably useless, but let's find out before making changes.

	/**
	 * Adds a DataProperty to the property index and adds it to the update index to be sent to players.
	 * @since API 3.0.0
	 *
	 * @param int          $key
	 * @param DataProperty $property
	 *
	 * @throws \InvalidArgumentException if the data property type is not what was expected by the server
	 */
	public function addProperty(int $key, DataProperty $property){
		if(!array_key_exists($key, self::$propertyIndex)){ //Unknown key, allow this in case it's a plugin adding properties the server doesn't know yet
			MainLogger::getLogger()->debug("Setting unknown data property " . $key . " with type " . $property->getType());

		}elseif(self::$propertyIndex[$key]->getType() !== $property->getType()){ //Type doesn't match what the server's expecting, throw an exception
			throw new \InvalidArgumentException(sprintf(
				"Invalid data property type supplied for key %d, expecting type %d, got type %d",
				$key,
				self::$propertyIndex[$key]->getType(),
				$property->getType()
			));
		}

		if(!array_key_exists($key, $this->dataProperties) or !$property->equals($this->dataProperties[$key])){
			$this->dataProperties[$key] = $property;
			$this->updateProperties[$key] = $property;
		}
	}

	/**
	 * Removes a DataProperty from the property index.
	 * @since API 3.0.0
	 *
	 * @param int $key
	 */
	public function removeProperty(int $key){
		if(array_key_exists($key, $this->dataProperties)){
			unset($this->dataProperties[$key]);
			$this->updateProperties[$key] = clone self::$propertyIndex[$key]; //Send original default to override previously set properties
		}
	}

	/**
	 * Returns a DataProperty from the index, or null if it does not exist.
	 * @since API 3.0.0
	 *
	 * @param int $key
	 *
	 * @return DataProperty|null
	 */
	public function getProperty(int $key){
		return $this->dataProperties[$key] ?? null;
	}

	/**
	 * Writes the specified properties to a BinaryStream
	 * @internal
	 *
	 * @param BinaryStream $stream
	 * @param bool         $writeAll Whether to write all metadata regardless of whether it is synchronized already
	 */
	public function writeTo(BinaryStream $stream, bool $writeAll = false){
		$properties = $writeAll ? $this->dataProperties : $this->updateProperties;

		$stream->putUnsignedVarInt(count($properties));
		foreach($properties as $key => $property){
			$stream->putUnsignedVarInt($key);
			$stream->putUnsignedVarInt($property->getType());
			$property->writeTo($stream);
		}
	}

	/**
	 * Reads entity data properties from a BinaryStream.
	 * @internal
	 *
	 * @param BinaryStream $stream
	 *
	 * @return EntityDataManager
	 */
	public static function readFrom(BinaryStream $stream) : EntityDataManager{
		$result = new EntityDataManager();

		try{
			$count = $stream->getUnsignedVarInt();
			for($i = 0; $i < $count; ++$i){

				$key = $stream->getUnsignedVarInt();
				$type = $stream->getUnsignedVarInt();

				if(!array_key_exists($type, self::$knownTypes)){
					throw new \UnexpectedValueException("Could not decode entity metadata: Unknown property type $type");
				}

				$property = clone self::$knownTypes[$type];

				$property->readFrom($stream);
				$result->addProperty($key, $property);
			}
		}catch(\Throwable $e){
			$logger = MainLogger::getLogger();
			$logger->logException($e);
		}

		return $result;
	}
}