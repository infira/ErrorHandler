<?php

namespace Infira\Error\Exception;

trait ThrowableDebugDataTrait
{
    private mixed $data = [];

    public function getDebugData(): mixed
    {
        return $this->data;
    }

    public function clearDebugData(): void
    {
        $this->data = [];
    }

    /** @inheritDoc */
    public function with(string|array $name, mixed $data = null): static
    {
        if (is_array($name) && $data === null) {
            foreach ($name as $k => $v) {
                $this->data[$k] = $v;
            }

            return $this;
        }
        $this->data[$name] = $data;

        return $this;
    }
}