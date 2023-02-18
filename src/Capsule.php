<?php

declare(strict_types=1);

namespace Infira\Error;

use Ramsey\Uuid\Uuid;

class Capsule implements \ArrayAccess
{
    private array $data = [];

    public function __construct(private ?string $name = null)
    {
        $this->name = $this->name ?: Uuid::uuid4()->toString();
    }

    /**
     * Add extra to error output for more extended information
     *
     * @param  string|array  $name  - string, or in case of array ,every key will be added as extra data key to error output
     * @param  mixed  $data  [$name=>$data] will be added to error output
     */
    public function put(string|array $name, mixed $data = null): static
    {
        if (is_array($name) && $data === null) {
            foreach ($name as $k => $v) {
                $this->put($k, $v);
            }

            return $this;
        }
        $this->data[$name] = $data;

        return $this;
    }

    public function push(mixed $data): static
    {
        array_push($this->data, ...$data);

        return $this;
    }

    public function mergeParent(self $capsule): static
    {
        return $this->put('parent-capsule('.$capsule->getName().')', $capsule->all());
    }

    public function all(): array
    {
        return $this->data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data);
    }

    public function getName(): string
    {
        return $this->name;
    }
}