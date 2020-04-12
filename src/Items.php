<?php

namespace ShabuShabu\Harness;

use ArrayAccess;
use Illuminate\Support\Arr;

class Items implements ArrayAccess
{
    protected Request $request;

    protected array $items;

    public function __construct(Request $request, array $items)
    {
        $this->request = $request;
        $this->items = Arr::dot($items);
    }

    /**
     * @param array $items
     * @return $this
     */
    public function set(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param array $items
     * @return $this
     */
    public function merge(array $items): self
    {
        $this->items = array_merge($this->items, $items);

        return $this;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($key, $value): void
    {
        $this->items[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }
}
