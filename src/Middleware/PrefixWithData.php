<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use ShabuShabu\Harness\Items;

class PrefixWithData
{
    /**
     * Handle an incoming request.
     *
     * @param Items    $items
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Items $items, Closure $next)
    {
        $data = $items->all();
        $values = array_combine(
            array_map(fn($k) => 'data.' . $k, array_keys($data)),
            $data
        );

        return $next($items->set($values));
    }
}
