<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use Illuminate\Support\Str;
use ShabuShabu\Harness\Items;

class HandleConfirmationRules
{
    /**
     * Handle an incoming request.s
     *
     * @param Items    $items
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Items $items, Closure $next)
    {
        $data   = $items->all();
        $values = array_combine(
            array_map(fn($k) => $this->transformConfirmation($k), array_keys($data)),
            $data
        );

        return $next($items->set($values));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function transformConfirmation(string $key): string
    {
        if (! Str::endsWith($key, 'Confirmation')) {
            return $key;
        }

        return Str::snake($key);
    }
}
