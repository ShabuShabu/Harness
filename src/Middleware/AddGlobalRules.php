<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use ShabuShabu\Harness\Items;

class AddGlobalRules
{
    /**
     * Handle an incoming request.
     *
     * @param Items    $rules
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Items $rules, Closure $next)
    {
        $type = $rules->request()->modelClass()::JSON_TYPE;

        return $next($rules->merge([
            'id'   => ['required', 'uuid'],
            'type' => ['required', 'in:' . $type],
        ]));
    }
}
