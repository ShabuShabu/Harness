<?php

namespace ShabuShabu\Harness\Tests;

use Illuminate\Http\Resources\MissingValue;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Harness\HarnessServiceProvider;
use ShabuShabu\Harness\Items;
use ShabuShabu\Harness\Middleware\{AddGlobalMessages,
    AddGlobalRules,
    HandleConfirmationRules,
    PrefixWithData,
    PrepareForPatching,
    RemoveMissingValues,
    TransformRulesets
};
use ShabuShabu\Harness\Tests\Support\RequestTrait;
use function ShabuShabu\Harness\r;

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
     * @test
     */
    public function ensure_that_global_messages_get_merged(): void
    {
        $middleware = new AddGlobalMessages();
        $messages   = new Items($this->request(), [
            'attributes' => [
                'title' => 'Awesome!',
            ],
        ]);

        $actual = $middleware->handle($messages, fn($v) => $v)->all();

        $expected = [
            'id.uuid'          => 'The ID must be a valid UUID',
            'id.required'      => 'An ID is required',
            'type.required'    => 'The type is required',
            'type.in'          => 'The type must be pages',
            'attributes.title' => 'Awesome!',
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function rulesProvider(): array
    {
        return [
            'ids are required'     => [true],
            'ids are not required' => [false],
        ];
    }

    /**
     * @test
     * @dataProvider rulesProvider
     * @param bool $requireIds
     */
    public function ensure_that_global_rules_get_merged($requireIds): void
    {
        $this->app['config']->set('harness.require_ids', $requireIds);

        $middleware = new AddGlobalRules();
        $rules      = new Items($this->request(), [
            'attributes' => [
                'title' => 'required',
            ],
        ]);

        $actual = $middleware->handle($rules, fn($v) => $v)->all();

        $expected = [
            'id'               => ['uuid'],
            'type'             => ['required', 'in:pages'],
            'attributes.title' => 'required',
        ];

        if ($requireIds) {
            $expected['id'][] = 'required';
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


    /**
     * @test
     * @group new
     */
    public function ensure_that_confirmation_rules_get_transformed(): void
    {
        $middleware = new HandleConfirmationRules();
        $messages   = new Items($this->request(), [
            'attributes' => [
                'password'             => 'Awesome',
                'passwordConfirmation' => 'Awesome',
            ],
        ]);

        $actual = $middleware->handle($messages, fn($v) => $v)->all();

        $expected = [
            'attributes.password'              => 'Awesome',
            'attributes.password_confirmation' => 'Awesome',
        ];

        $this->assertEquals($expected, $actual);
    }
}
