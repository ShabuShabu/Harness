<?php

namespace ShabuShabu\Harness\Middleware;

use Closure;
use Illuminate\Support\Arr;
use ShabuShabu\Harness\Items;

class PrepareForPatching
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
        if (! $rules->request()->patching()) {
            return $next($rules);
        }

        return $next($rules->set(
            array_map(fn (array $items) => $this->swapRequiredForNullable($items), $rules->all())
        ));
    }

    /**
     * @param array $items
     * @return array
     */
    protected function swapRequiredForNullable(array $items): array
    {
        $items = array_map(
            fn (string $rule) => $rule === 'required' ? 'nullable' : $rule,
            $items
        );

        return array_unique(
            Arr::prepend($items, 'nullable')
        );
    }
}
