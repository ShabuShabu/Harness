<?php

namespace ShabuShabu\Harness\Tests\Support;

use ShabuShabu\Harness\Request;
use function ShabuShabu\Harness\r;

class PageRequest extends Request
{
    public function ruleset(): array
    {
        return [
            'attributes' => [
                'title'        => r()->required()->string(),
                'content'      => r()->required()->string(),
                'published_at' => r()->nullable()->dateFormat('Y-m-d H:i:s'),
            ],
        ];
    }

    public function feedback(): array
    {
        return [
            'attributes' => [
                'title'        => [
                    'required' => 'The title is required',
                    'string'   => 'The title must be a string',
                ],
                'content'      => [
                    'required' => 'The content field is required',
                    'string'   => 'The content field must be a string',
                ],
                'published_at' => [
                    'date_format' => 'The publish date is invalid',
                ],
            ],
        ];
    }
}
