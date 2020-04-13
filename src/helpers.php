<?php

declare(strict_types=1);

namespace ShabuShabu\Harness;

use Illuminate\Support\Str;
use InvalidArgumentException;

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

/**
 * Retrieve the json type from the model
 *
 * @param \ShabuShabu\Harness\Items $items
 * @return string
 */
function json_type(Items $items): string
{
    $model = $items->request()->guessModel();

    $jsonType = $model . '::JSON_TYPE';

    if (! defined($jsonType)) {
        throw new InvalidArgumentException("The JSON_TYPE constant was not set on [$model]");
    }

    return constant($jsonType);
}
