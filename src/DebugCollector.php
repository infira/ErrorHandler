<?php

declare(strict_types=1);

namespace Infira\Error;

class DebugCollector
{
    private static array $data = [];
    private static array $capsuleData = [];
    private static string|null $capsuleID = null;

    public static function set(string $name, mixed $value): void
    {
        if (self::$capsuleID) {
            self::$capsuleData[self::$capsuleID][$name] = $value;

            return;
        }
        self::$data[$name] = $value;
    }

    public static function all(string $capsuleID = null): array
    {
        if ($capsuleID !== null) {
            return self::$capsuleData[$capsuleID] ?? [];
        }

        return self::$data;
    }

    public static function flush(): void
    {
        self::$data = [];
    }

    public static function setCapsuleID(string $capsuleID): void
    {
        self::$capsuleID = $capsuleID;
    }

    public static function clearCapsule(string $capsuleID): void
    {
        self::$capsuleID = null;
        if (isset(self::$capsuleData[$capsuleID])) {
            unset(self::$capsuleData[$capsuleID]);
        }
    }

    public static function getCapsuleID(): ?string
    {
        return self::$capsuleID;
    }

}