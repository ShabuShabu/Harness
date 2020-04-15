<?php

namespace ShabuShabu\Harness\Tests;

use Illuminate\Http\Resources\MissingValue;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Harness\HarnessServiceProvider;
use ShabuShabu\Harness\Items;
use ShabuShabu\Harness\Middleware\{AddGlobalMessages,
    AddGlobalRules,
    PrefixWithData,
    PrepareForPatching,
    RemoveMissingValues,
    TransformRulesets
};
use function ShabuShabu\Harness\r;
use ShabuShabu\Harness\Tests\Support\RequestTrait;

class MiddlewareTest extends TestCase
{
    use RequestTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('harness.model_namespace', __NAMESPACE__ . '\\Support');
    }

    protected function getPackageProviders($app): array
    {
        return [HarnessServiceProvider::class];
    }

    /**
     * @return array
     */
    public function globalProvider(): array
    {
        return [
            'uuids are used'       => [true],
            'integer ids are used' => [false],
        ];
    }

    /**
     * @test
     * @dataProvider globalProvider
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

    /**
     * @test
     * @dataProvider  globalProvider
     * @param bool $useUuids
     */
    public function ensure_that_global_rules_get_merged(bool $useUuids): void
    {
        $this->app['config']->set('harness.use_uuids', $useUuids);

        $middleware = new AddGlobalRules();
        $rules      = new Items($this->request(), [
            'attributes' => [
                'title' => 'required',
            ],
        ]);

        $actual = $middleware->handle($rules, fn($v) => $v)->all();

        $expected = [
            'attributes.title' => 'required',
            'id'               => ['required'],
            'type'             => ['required', 'in:pages'],
        ];

        if ($useUuids) {
            $expected['id'][] = 'uuid';
        } else {
            $expected['id'][] = 'integer';
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_items_get_prefixed_with_data(): void
    {
        $middleware = new PrefixWithData();
        $rules      = new Items($this->request(), [
            'attributes' => [
                'title' => 'required',
            ],
        ]);

        $actual = $middleware->handle($rules, fn($v) => $v)->all();

        $expected = [
            'data.attributes.title' => 'required',
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_patch_specific_middleware_is_skipped(): void
    {
        $middleware = new PrepareForPatching();
        $rules      = new Items($this->request('PUT'), [
            'attributes' => [
                'title' => 'required',
            ],
        ]);

        $actual = $middleware->handle($rules, fn($v) => $v)->all();

        $expected = [
            'attributes.title' => 'required',
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_items_get_prepared_for_patching(): void
    {
        $middleware = new PrepareForPatching();
        $rules      = (new Items($this->request('PATCH'), []))->set([
            'attributes.title'   => ['required'],
            'attributes.content' => ['string'],
        ]);

        $actual = $middleware->handle($rules, fn($v) => $v)->all();

        $expected = [
            'attributes.title'   => ['nullable'],
            'attributes.content' => ['nullable', 'string'],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_missing_values_get_removed(): void
    {
        $middleware = new RemoveMissingValues();
        $rules      = (new Items($this->request(), []))->set([
            'attributes.title' => new MissingValue(),
            'attributes.seo'   => [
                'title'       => new MissingValue(),
                'description' => 'ha',
            ],
        ]);

        $actual = $middleware->handle($rules, fn($v) => $v)->all();

        $expected = [
            'attributes.seo' => [
                'description' => 'ha',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_rulesets_are_transformed(): void
    {
        $middleware = new TransformRulesets();
        $rules      = new Items($this->request(), [
            'attributes' => [
                'title' => r('required', 'string'),
            ],
        ]);

        $actual = $middleware->handle($rules, fn($v) => $v)->all();

        $expected = [
            'attributes.title' => [
                'required',
                'string',
            ],
        ];

        $this->assertEquals($expected, $actual);
    }
}
