<?php

namespace ShabuShabu\Harness\Tests;

use PHPUnit\Framework\TestCase;
use ShabuShabu\Harness\Items;
use ShabuShabu\Harness\Tests\Support\RequestTrait;

class ItemsTest extends TestCase
{
    use RequestTrait;

    /**
     * @test
     */
    public function ensure_that_items_get_dotted_during_instantiation(): void
    {
        $items = new Items($this->request(), [
            'some' => [
                'field'  => true,
                'other'  => false,
                'nested' => [
                    'deeper' => 'yup',
                ],
            ],
        ]);

        $this->assertEquals([
            'some.field'         => true,
            'some.other'         => false,
            'some.nested.deeper' => 'yup',
        ], $items->all());
    }

    /**
     * @test
     */
    public function ensure_that_the_request_gets_set(): void
    {
        $request = $this->request();

        $items = new Items($request, []);

        $this->assertSame($items->request(), $request);
    }

    /**
     * @test
     */
    public function ensure_that_items_can_be_replaced(): void
    {
        $items = new Items($this->request(), [
            'deeper' => 'yup',
        ]);

        $this->assertEquals(['deeper' => 'yup'], $items->all());

        $items->set(['field' => true]);

        $this->assertEquals(['field' => true], $items->all());
    }

    /**
     * @test
     */
    public function ensure_that_items_can_be_merged(): void
    {
        $items = new Items($this->request(), [
            'deeper' => 'yup',
        ]);

        $this->assertEquals(['deeper' => 'yup'], $items->all());

        $items->merge(['field' => true]);

        $this->assertEquals(['deeper' => 'yup', 'field' => true], $items->all());
    }

    /**
     * @test
     */
    public function ensure_that_items_has_array_access(): void
    {
        $items = new Items($this->request(), [
            'deeper' => 'yup',
            'field'  => true,
        ]);

        $this->assertTrue($items->offsetExists('deeper'));
        $this->assertTrue(isset($items['deeper']));

        $this->assertFalse($items->offsetExists('higher'));
        $this->assertFalse(isset($items['higher']));

        $this->assertEquals('yup', $items->offsetGet('deeper'));
        $this->assertEquals('yup', $items['deeper']);

        $this->assertEquals(null, $items->offsetGet('higher'));
        $this->assertEquals(null, $items['higher']);

        $items->offsetUnset('deeper');
        unset($items['field']);

        $this->assertEquals([], $items->all());

        $items->offsetSet('low', 'ball');
        $items['ball'] = 'low';

        $this->assertEquals(['low' => 'ball', 'ball' => 'low'], $items->all());
    }
}
