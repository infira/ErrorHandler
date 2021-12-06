<?php declare(strict_types=1);

namespace Infira\Error;

class ErrorData
{
	private static $data = [];
	
	public static function set(string $name, $value)
	{
		self::$data[$name] = $value;
	}
	
	public static function getAll(): array
	{
		return self::$data;
	}
	
	public static function flush()
	{
		self::$data = [];
	}
}