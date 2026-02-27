<?php

namespace Asinka;

use Random\RandomException;

/**
 * Class UAHelper
 * @package Asinka
 */
class UAHelper
{
	/**
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function randomInt(int $min, int $max): int
	{
		try {
			return random_int($min, $max);
		} catch (RandomException) {
			return $min;
		}
	}

	/**
	 * @param array $array
	 * @return mixed
	 */
	public static function arrayRandom(array $array): mixed
	{
		return $array[array_rand($array, 1)];
	}
}