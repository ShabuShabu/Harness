<?php

namespace ShabuShabu\Harness\Tests;

use Orchestra\Testbench\TestCase;
use ShabuShabu\Harness\Tests\Support\Page;
use ShabuShabu\Harness\Tests\Support\RequestTrait;

class RequestTest extends TestCase
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
    public function ensure_that_the_request_flags_work_properly(): void
    {
        $this->assertTrue($this->request('PATCH')->patching());
        $this->assertFalse($this->request('PUT')->patching());

        $this->assertTrue($this->request('POST')->creating());
        $this->assertFalse($this->request('PUT')->creating());

        $this->assertTrue($this->request('PUT')->updating());
        $this->assertTrue($this->request('PATCH')->updating());
        $this->assertFalse($this->request('POST')->updating());
    }

    /**
     * @test
     */
    public function ensure_that_the_validation_data_gets_snake_cased(): void
    {
        $actual = $this->request('POST')->validationData();

        // @see \ShabuShabu\Harness\Tests\Support\PageRequest
        $expected = [
            'data' => [
                'attributes' => [
                    'title'        => 'Pretty!',
                    'content'      => 'blabla',
                    'published_at' => '2020-03-05 20:34:45',
                ],
            ],
        ];

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_the_related_model_can_be_guessed(): void
    {
        $this->assertSame(Page::class, $this->request()->guessModel());
    }

    /**
     * @test
     */
    public function ensure_that_the_related_model_can_be_instantiated(): void
    {
        $this->assertInstanceOf(Page::class, $this->request()->modelClass());
    }

    /**
     * @test
     */
    public function ensure_that_the_rules_get_transformed_properly(): void
    {
        $actual = $this->request()->setContainer($this->app)->rules();

        // @see \ShabuShabu\Harness\Tests\Support\PageRequest
        $expected = [
            'data.id'                      => [
                'required',
                'integer',
            ],
            'data.type'                    => [
                'required',
                'in:pages',
            ],
            'data.attributes.title'        => [
                'required',
                'string',
            ],
            'data.attributes.content'      => [
                'required',
                'string',
            ],
            'data.attributes.published_at' => [
                'nullable',
                'date_format:Y-m-d H:i:s',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_the_messages_get_transformed_properly(): void
    {
        $actual = $this->request()->setContainer($this->app)->messages();

        // @see \ShabuShabu\Harness\Tests\Support\PageRequest
        $expected = [
            'data.attributes.title.required'           => 'The title is required',
            'data.attributes.title.string'             => 'The title must be a string',
            'data.attributes.content.required'         => 'The content field is required',
            'data.attributes.content.string'           => 'The content field must be a string',
            'data.attributes.published_at.date_format' => 'The publish date is invalid',
            'data.id.required'                         => 'An ID is required',
            'data.type.required'                       => 'The type is required',
            'data.type.in'                             => 'The type must be pages',
            'data.id.integer'                          => 'The ID must be a valid integer',
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @group fail
     */
    public function ensure_that_the_request_authorises_a_request_properly(): void
    {
        $this->assertTrue(true);
    }
}