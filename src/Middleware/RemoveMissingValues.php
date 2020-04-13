<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use Illuminate\Http\Resources\MissingValue;
use ShabuShabu\Harness\Items;

class RemoveMissingValues
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
        $items = $rules->all();

        return $next($rules->set(
            $this->replace($items)
        ));
    }

    /**
     * @param $rules
     * @return array
     */
    protected function replace(&$rules): array
    {
        foreach ($rules as $key => $rule) {
            if ($rule instanceof MissingValue) {
                unset($rules[$key]);
            }

            if (is_array($rule)) {
                $rules[$key] = $this->replace($rule);
            }
        }

        return $rules;
    }
}
