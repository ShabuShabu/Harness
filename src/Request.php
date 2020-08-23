<?php

namespace ShabuShabu\Harness;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ShabuShabu\Harness\Middleware\{AddGlobalMessages,
    AddGlobalRules,
    HandleConfirmationRules,
    PrefixWithData,
    PrepareForPatching,
    RemoveMissingValues,
    TransformRulesets
};

abstract class Request extends FormRequest
{
    /**
     * @return string
     */
    public function guessModel(): string
    {
        $namespace = config('harness.model_namespace');

        return $namespace . '\\' . str_replace('Request', '', class_basename(static::class));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if (! $this->user()) {
            return false;
        }

        if ($this->creating()) {
            return $this->user()->can('create', $this->guessModel());
        }

        return $this->user()->can('update', $this->routeModel());
    }

    /**
     * @return \Illuminate\Routing\Route|mixed|object|string
     */
    protected function routeModel()
    {
        $model = $this->guessModel();

        if (! method_exists($model, 'routeParam')) {
            throw new InvalidArgumentException("The ROUTE_PARAM constant was not set on [$model]");
        }

        return $this->route($model::routeParam());
    }

    /**
     * @return array
     */
    abstract public function ruleset(): array;

    /**
     * @return array
     */
    public function feedback(): array
    {
        return [];
    }

    /**
     * For now this is fine, but if we need to do more work here, then
     * we should consider a pipeline analogue to the rules and messages
     * {@inheritDoc}
     */
    public function validationData(): array
    {
        $data = parent::validationData();

        $transform = fn($k) => Str::endsWith($k, 'Confirmation') ? Str::snake($k) : $k;

        $attr = Arr::get($data, 'data.attributes', []);

        $attributes = array_combine(
            array_map(fn($k) => $transform($k), array_keys($attr)),
            $attr
        );

        Arr::set($data, 'data.attributes', $attributes);

        return $data;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->pipeline($this->ruleset(), [
            TransformRulesets::class,
            HandleConfirmationRules::class,
            RemoveMissingValues::class,
            PrepareForPatching::class,
            AddGlobalRules::class,
            PrefixWithData::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function messages(): array
    {
        if (count($feedback = $this->feedback()) <= 0) {
            return [];
        }

        return $this->pipeline($feedback, [
            AddGlobalMessages::class,
            PrefixWithData::class,
        ]);
    }

    /**
     * @param array $items
     * @param array $pipes
     * @return array
     */
    protected function pipeline(array $items, array $pipes): array
    {
        return (new Pipeline($this->container))
            ->send(new Items($this, $items))
            ->through($pipes)
            ->then(fn(Items $items) => $items->all());
    }

    /**
     * Check if we are on a create request
     *
     * @return bool
     */
    public function creating(): bool
    {
        return $this->isMethod('POST');
    }

    /**
     * Check if we are on a patch request
     *
     * @return bool
     */
    public function patching(): bool
    {
        return $this->isMethod('PATCH');
    }

    /**
     * Check if we are on an update request
     *
     * @return bool
     */
    public function updating(): bool
    {
        return in_array($this->method(), ['PUT', 'PATCH'], true);
    }
}
