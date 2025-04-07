<?php

declare(strict_types=1);

namespace Infira\Error;

use Ramsey\Uuid\Uuid;
use Throwable;

/**
 * @mixin ErrorDataCollection
 */
class Capsule extends ErrorDataCollection
{
    private string $name;
    private array $onCatch = [];
    private ?Capsule $parent = null;
    private bool $hasName = false;
    private array $trace = [];

    public function __construct(?string $name = null)
    {
        if ($name !== null) {
            $this->hasName = true;
        }
        else {
            $name = Uuid::uuid4()->toString();
        }
        $this->name = $name;
    }

    public function __debugInfo(): ?array
    {
        return [
            $this->name => [
                'data' => $this->getRealData(),
                'parent' => $this->parent ?? 'null',
            ]
        ];
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        $this->hasName = true;
        return $this;
    }

    public function setTrace(array $trace): static
    {
        $this->trace = array_values($trace);
        return $this;
    }

    public function getTrace(): array
    {
        return $this->trace;
    }

    public static function make(?string $name = null): static
    {
        return new static($name);
    }

    /** @internal */
    public function addParent(self $capsule): void
    {
        if ($this->parent) {
            $this->parent->addParent($capsule);
            return;
        }
        $this->parent = $capsule;
    }

    public function all(bool $withName = true): array
    {
        $data = parent::all();

        if ($this->parent) {
            $data['parent.capsule=>('.$this->parent->getDebugName().')'] = $this->parent->all(false);
        }

        if (!$withName) {
            return $data;
        }

        return ['capsule=>('.$this->getDebugName().')' => $data];
    }

    private function getDebugName(): string
    {
        $name = '';
        if ($this->hasName) {
            $name = $this->name;
        }
        if (isset($this->trace[0])) {
            $calledFrom = $this->trace[0];
            $root = $_SERVER['DOCUMENT_ROOT'] ?? null;
            $trace = $calledFrom['file'];
            if ($root) {
                $trace = './'.str_replace(
                        str_replace('\\', '/', $root.'/'),
                        '',
                        $trace
                    );
            }
            $trace .= ':'.$calledFrom['line'];
            $name = $name ? $name.'@'.$trace : $trace;
        }
        return $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function onCatch(callable $callback): static
    {
        $this->onCatch[] = $callback;
        return $this;
    }

    public function executeOnCatch(Throwable $exception): void
    {
        foreach ($this->onCatch as $callback) {
            $callback($this, $exception);
        }
    }
}