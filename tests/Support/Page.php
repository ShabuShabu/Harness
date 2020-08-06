<?php

namespace ShabuShabu\Harness\Tests\Support;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public const JSON_TYPE   = 'pages';
    public const ROUTE_PARAM = 'page';

    public static function jsonType(): string
    {
        return static::JSON_TYPE;
    }
}
