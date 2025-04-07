<?php

declare(strict_types=1);

namespace Infira\Error;

class ErrorDataCollection implements \ArrayAccess
{
    private array $data = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Add extra to error output for more extended information
     *
     * @param string|array $key - string, or in case of array ,every key will be added as extra data key to error output
     * @param mixed $data [$name=>$data] will be added to error output
     */
    public function put(string|array $key, mixed $data = null): static
    {
        if (is_array($key) && $data === null) {
            foreach ($key as $k => $v) {
                $this->put($k, $v);
            }

            return $this;
        }
        $this->data[$key] = $data;

        return $this;
    }

    public function push(mixed $data): static
    {
        array_push($this->data, ...$data);

        return $this;
    }

    public function pushTo(string|array $to, mixed $data): static
    {
        $ref = &$this->data;
        foreach ((array)$to as $key) {
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                $ref[$key] = [];
            }
            $ref = &$ref[$key];
        }
        $ref = $data;
        return $this;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function getRealData(): array
    {
        return $this->data;
    }

    public function flush(): static
    {
        $this->data = [];

        return $this;
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
}