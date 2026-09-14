<?php

declare(strict_types=1);

namespace App\UserInterface\GraphQL\Config;

use Illuminate\Contracts\Config\Repository;

final class ArrayRepository implements Repository
{
    /** @param array<string, mixed> $items */
    public function __construct(private array $items) {}

    public function has($key): bool
    {
        $segments = explode('.', (string) $key);
        $value = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }
            $value = $value[$segment];
        }

        return true;
    }

    public function get($key, $default = null): mixed
    {
        if (is_array($key)) {
            // illuminate contract allows array key for multi-get; not needed here
            return $default;
        }

        $segments = explode('.', $key);
        $value = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function set($key, $value = null): void
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
            return;
        }

        $segments = explode('.', $key);
        $ref = &$this->items;

        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                $ref[$segment] = $value;
            } else {
                if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                    $ref[$segment] = [];
                }
                $ref = &$ref[$segment];
            }
        }
    }

    public function prepend($key, $value): void
    {
        $array = $this->get($key, []);
        array_unshift($array, $value);
        $this->set($key, $array);
    }

    public function push($key, $value): void
    {
        $array = $this->get($key, []);
        $array[] = $value;
        $this->set($key, $array);
    }
}
