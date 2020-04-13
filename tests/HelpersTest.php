<?php

namespace ShabuShabu\Harness\Tests;

use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Harness\Items;
use ShabuShabu\Harness\Request;
use ShabuShabu\Harness\Tests\Support\RequestTrait;
use function ShabuShabu\Harness\{json_type, to_snake_case};
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class HelpersTest extends TestCase
{
    use RequestTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set(
            'harness.model_namespace',
            'ShabuShabu\\Harness\\Tests\\Support'
        );
    }

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

    /**
     * @test
     */
    public function ensure_that_a_json_type_can_be_fetched(): void
    {
        $items = new Items($this->request(), []);

        $this->assertSame('pages', json_type($items));
    }

    /**
     * @test
     */
    public function ensure_that_a_missing_json_type_causes_an_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $testRequest = new class extends Request {
            public function ruleset(): array
            {
                return [];
            }

            public function feedback(): array
            {
                return [];
            }
        };

        $request = $testRequest::createFromBase(
            BaseRequest::create('', 'POST', [], [], [], [], null)
        );

        $items = new Items($request, []);

        json_type($items);
    }
}
