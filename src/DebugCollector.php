<?php

declare(strict_types=1);

namespace Infira\Error;

class DebugCollector
{
    private static array $data = [];
    /**
     * @var Capsule[];
     */
    private static array $capsules = [];
    private static string|null $capsuleID = null;

    /**
     * Add extra to error output for more extended information
     *
     * @param  string|array  $name  - string, or in case of array ,every key will be added as extra data key to error output
     * @param  mixed  $data  [$name=>$data] will be added to error output
     */
    public static function set(string|array $name, mixed $data = null): void
    {
        if (is_array($name) && $data === null) {
            foreach ($name as $k => $v) {
                self::$data[$k] = $v;
            }

            return;
        }
        self::$data[$name] = $data;
    }

    public static function all(): array
    {
        return self::$data;
    }

    public static function flush(): void
    {
        self::$data = [];
    }

    public static function setCapsuleID(?string $capsuleID): void
    {
        self::$capsuleID = $capsuleID;
    }

    public static function makeCapsule(?string $capsuleID): Capsule
    {
        if (self::$capsuleID) {
            return self::$capsules[$capsuleID]['subCapsule'] = new Capsule();
        }

        return self::$capsules[$capsuleID] = new Capsule();
    }

    public static function clearCapsule(?string $capsuleID, ?string $setNewCapsuleID): void
    {
        self::$capsuleID = $setNewCapsuleID;
        if (isset(self::$capsules[$capsuleID])) {
            unset(self::$capsules[$capsuleID]);
        }
    }

    public static function getActiveCapsuleID(): ?string
    {
        return self::$capsuleID;
    }
}