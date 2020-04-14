<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use ShabuShabu\Harness\{Items, Ruleset};

class TransformRulesets
{
    /**
     * Handle an incoming request.
     *
     * @param \ShabuShabu\Harness\Items $items
     * @param \Closure                  $next
     * @return mixed
     */
    public function handle(Items $items, Closure $next)
    {
        return $next($items->set(
            array_map(fn (Ruleset $rule) => $rule->all(), $items->all())
        ));
    }
}
