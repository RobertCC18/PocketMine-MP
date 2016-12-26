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

use pocketmine\utils\BinaryStream;

/**
 * @since API 3.0.0
 */
abstract class DataProperty{

	const DATA_TYPE = -1;

	/**
	 * Encodes the data property to binary.
	 * @internal
	 *
	 * @param BinaryStream $stream
	 */
	abstract public function writeTo(BinaryStream $stream);

	/**
	 * Decodes a metadata property from binary.
	 * @internal
	 *
	 * @param BinaryStream $stream
	 */
	abstract public function readFrom(BinaryStream $stream);

	/**
	 * Returns whether the value of two data properties are equivalent
	 * @since API 3.0.0
	 *
	 * @param DataProperty $property
	 *
	 * @return bool
	 */
	abstract public function equals(DataProperty $property) : bool;

	/**
	 * Returns the value of the data property
	 * @since API 3.0.0
	 *
	 * @return mixed
	 */
	abstract public function getValue();

	/**
	 * Sets the value of the data property
	 * @since API 3.0.0
	 *
	 * @param mixed $value
	 */
	abstract public function setValue($value);

	/**
	 * Returns the metadata network type
	 * @internal
	 *
	 * @return int
	 */
	final public function getType() : int{
		return static::DATA_TYPE;
	}
}