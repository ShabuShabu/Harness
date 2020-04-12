<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use ShabuShabu\Harness\Items;

class AddGlobalMessages
{
    /**
     * Handle an incoming request.
     *
     * @param Items    $messages
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Items $messages, Closure $next)
    {
        $type = $messages->request()->modelClass()::JSON_TYPE;

        return $next($messages->merge([
            'id.required'   => 'An ID is required',
            'id.uuid4'      => 'The ID must be a valid UUID',
            'type.required' => 'The type is required',
            'type.in'       => 'The type must be ' . $type,
        ]));
    }
}
