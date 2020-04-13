<?php

namespace ShabuShabu\Harness\Tests;

use Orchestra\Testbench\TestCase;
use ShabuShabu\Harness\Items;
use ShabuShabu\Harness\Middleware\AddGlobalMessages;
use ShabuShabu\Harness\Tests\Support\RequestTrait;

class MiddlewareTest extends TestCase
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
     * @return array
     */
    public function globalMessageProvider(): array
    {
        return [
            'uuids are used'       => [true],
            'integer ids are used' => [false],
        ];
    }

    /**
     * @test
     * @dataProvider globalMessageProvider
     * @param bool $useUuids
     */
    public function ensure_that_global_messages_get_merged(bool $useUuids): void
    {
        $this->app['config']->set('harness.use_uuids', $useUuids);

        $middleware = new AddGlobalMessages();
        $messages   = new Items($this->request(), [
            'attributes' => [
                'title' => 'Awesome!',
            ],
        ]);

        $actual = $middleware->handle($messages, fn($v) => $v)->all();

        $expected = [
            'id.required'      => 'An ID is required',
            'type.required'    => 'The type is required',
            'type.in'          => 'The type must be pages',
            'attributes.title' => 'Awesome!',
        ];

        if ($useUuids) {
            $expected['id.uuid'] = 'The ID must be a valid UUID';
        } else {
            $expected['id.integer'] = 'The ID must be a valid integer';
        }

        $this->assertEquals($expected, $actual);
    }
}
