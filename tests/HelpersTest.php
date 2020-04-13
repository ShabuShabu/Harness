<?php

namespace ShabuShabu\Harness\Tests;

use PHPUnit\Framework\TestCase;
use function ShabuShabu\Harness\to_snake_case;

class HelpersTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_that_array_keys_can_be_transformed_to_snake_case(): void
    {
        $input = [
            'keyOne' => 'test',
            'keyTwo' => [
                'key_three' => 'foo',
                'keyFour'   => 'bar',
            ],
        ];

        $expected = [
            'key_one' => 'test',
            'key_two' => [
                'key_three' => 'foo',
                'key_four'  => 'bar',
            ],
        ];

        $this->assertSame($expected, to_snake_case($input));
    }
}
