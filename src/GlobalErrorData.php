<?php declare(strict_types=1);

namespace Infira\Error;

class GlobalErrorData
{
	private static array $data = [];
	
	public static function set(string $name, $value)
	{
		self::$data[$name] = $value;
	}
	
	public static function all(): array
	{
		return self::$data;
	}
	
	public static function flush()
	{
		self::$data = [];
	}
}