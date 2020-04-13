<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use ShabuShabu\Harness\Items;
use function ShabuShabu\Harness\json_type;

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
        $globalRules = [
            'id'   => ['required'],
            'type' => ['required', 'in:' . json_type($rules)],
        ];

        $globalRules['id'][] = config('harness.use_uuids') ? 'uuid' : 'integer';

        return $next($rules->merge($globalRules));
    }
}
