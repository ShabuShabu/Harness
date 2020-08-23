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
            'id'   => ['uuid'],
            'type' => ['required', 'in:' . json_type($rules)],
        ];

        if (config('harness.require_ids')) {
            $globalRules['id'][] = 'required';
        }

        return $next($rules->merge($globalRules));
    }
}
