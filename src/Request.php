<?php

namespace ShabuShabu\Harness;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pipeline\Pipeline;
use InvalidArgumentException;
use ShabuShabu\Harness\Middleware\{AddGlobalMessages,
    AddGlobalRules,
    PrefixWithData,
    PrepareForPatching,
    RemoveMissingValues,
    TransformRulesets
};

abstract class Request extends FormRequest
{
    /**
     * @return Model
     */
    public function modelClass(): Model
    {
        $model = $this->guessModel();

        return new $model();
    }

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
        $model      = $this->modelClass();
        $routeParam = $model . '::ROUTE_PARAM';

        if (! defined($routeParam)) {
            throw new InvalidArgumentException("The ROUTE_PARAM constant was not set on [$model]");
        }

        return $this->route(constant($routeParam));
    }

    /**
     * @return array
     */
    abstract public function ruleset(): array;

    /**
     * @return array
     */
    abstract public function feedback(): array;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->pipeline($this->ruleset(), [
            TransformRulesets::class,
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
        return $this->pipeline($this->feedback(), [
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
            ->then(fn (Items $items) => $items->all());
    }

    /**
     * {@inheritdoc}
     */
    public function validationData(): array
    {
        return to_snake_case(parent::validationData());
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
