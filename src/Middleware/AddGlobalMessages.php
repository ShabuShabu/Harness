<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use ShabuShabu\Harness\Items;
use function ShabuShabu\Harness\json_type;

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
        $feedback = [
            'id.required'   => 'An ID is required',
            'type.required' => 'The type is required',
            'type.in'       => 'The type must be ' . json_type($messages),
        ];

        if (config('harness.use_uuids')) {
            $feedback['id.uuid'] = 'The ID must be a valid UUID';
        } else {
            $feedback['id.integer'] = 'The ID must be a valid integer';
        }

        return $next($messages->merge($feedback));
    }
}
