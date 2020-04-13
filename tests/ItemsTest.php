<?php

namespace ShabuShabu\Harness\Tests;

use Orchestra\Testbench\TestCase;
use ShabuShabu\Harness\{Items, Request};
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class ItemsTest extends TestCase
{
    /**
     * @test
     * @gorup fail
     */
    public function ensure_that_an_items_object_can_be_instantiated(): void
    {
        $items = new Items($this->request('POST'), [
            'some' => [
                'field' => true,
                'other' => false,
            ],
        ]);

        $this->assertEquals([
            'some.field' => true,
            'some.other' => false,
        ], $items->all());
    }

    protected function request(string $method): Request
    {
        return Request::createFromBase(
            BaseRequest::create('', $method, [], [], [], [], null)
        );
    }
}
