<?php

declare(strict_types=1);

namespace ShabuShabu;

use Illuminate\Support\Str;
use ShabuShabu\Harness\Ruleset;

/**
 * @param mixed ...$rules
 * @return \ShabuShabu\Harness\Ruleset
 */
function r(...$rules): Ruleset
{
    return new Ruleset($rules);
}

/**
 * Transform array keys to snake_case
 *
 * @param array $data
 * @return array
 */
function to_snake_case(array $data): array
{
    $out = [];
    foreach ($data as $key => $sub) {
        $out[Str::snake($key)] = is_array($sub) ? to_snake_case($sub) : $sub;
    }

    return $out;
}

