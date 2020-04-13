<?php

namespace ShabuShabu\Harness;

use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @method Ruleset accepted()
 * @method Ruleset activeUrl()
 * @method Ruleset after(string $field)
 * @method Ruleset afterOrEqual(string $field)
 * @method Ruleset alpha()
 * @method Ruleset alphaDash()
 * @method Ruleset alphaNum()
 * @method Ruleset array()
 * @method Ruleset bail()
 * @method Ruleset before(string $field)
 * @method Ruleset beforeOrEqual(string $field)
 * @method Ruleset between(int $min, int $max)
 * @method Ruleset boolean()
 * @method Ruleset confirmed()
 * @method Ruleset date()
 * @method Ruleset dateEquals(string $date)
 * @method Ruleset dateFormat(string $format)
 * @method Ruleset different(string $field)
 * @method Ruleset digits(int $length)
 * @method Ruleset digitsBetween(int $min, int $max)
 * @method Ruleset dimensions(array $config)
 * @method Ruleset distinct()
 * @method Ruleset email(string $validator = 'rfc')
 * @method Ruleset endsWith(...$values)
 * @method Ruleset excludeIf(string $field, $value)
 * @method Ruleset excludeUnless(string $field, $value)
 * @method Ruleset exists(string $table, string $column)
 * @method Ruleset file()
 * @method Ruleset filled()
 * @method Ruleset gt(string $field)
 * @method Ruleset gte(string $field)
 * @method Ruleset image()
 * @method Ruleset in(array $options)
 * @method Ruleset inArray(string $field)
 * @method Ruleset integer()
 * @method Ruleset ip()
 * @method Ruleset ipv4()
 * @method Ruleset ipv6()
 * @method Ruleset json()
 * @method Ruleset latitude()
 * @method Ruleset longitude()
 * @method Ruleset lt(string $field)
 * @method Ruleset lte(string $field)
 * @method Ruleset max(int $number)
 * @method Ruleset mimes(array $options)
 * @method Ruleset mimetypes(array $options)
 * @method Ruleset min(int $number)
 * @method Ruleset notIn(array $options)
 * @method Ruleset notRegex(string $pattern)
 * @method Ruleset nullable()
 * @method Ruleset numeric()
 * @method Ruleset password()
 * @method Ruleset present()
 * @method Ruleset regex(string $pattern)
 * @method Ruleset required()
 * @method Ruleset requiredIf(...$config)
 * @method Ruleset requiredUnless(...$config)
 * @method Ruleset requiredWith(string ...$field)
 * @method Ruleset requiredWithAll(string ...$field)
 * @method Ruleset requiredWithout(string ...$field)
 * @method Ruleset requiredWithoutAll(string ...$field)
 * @method Ruleset same(string $field)
 * @method Ruleset size(int $value)
 * @method Ruleset sometimes()
 * @method Ruleset startsWith(string ...$values)
 * @method Ruleset string()
 * @method Ruleset timezone()
 * @method Ruleset url()
 * @method Ruleset uuid()
 */
class Ruleset
{
    /**
     * @var array|MissingValue
     */
    protected $items;

    /**
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param bool $condition
     * @return $this
     */
    public function when(bool $condition): self
    {
        if (! $condition) {
            $this->items = new MissingValue;
        }

        return $this;
    }

    /**
     * @param bool $condition
     * @return $this
     */
    public function unless(bool $condition): self
    {
        return $this->when(! $condition);
    }

    /**
     * @param mixed $rule
     * @param bool  $condition
     * @return $this
     */
    public function push($rule, bool $condition = true): self
    {
        if ($condition && ! $this->items instanceof MissingValue) {
            $this->items[] = $rule;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param string $table
     * @param string $column
     * @param bool   $condition
     * @param string $except
     * @return $this
     */
    public function unique(string $table, string $column, bool $condition, string $except = ''): self
    {
        $rule = Rule::unique($table, $column);

        if ($except) {
            $rule = $rule->ignore($except, $column);
        }

        return $this->push($rule, $condition);
    }

    /**
     * @param string $name
     * @param bool   $condition
     * @return $this
     */
    public function removeRuleIf(string $name, bool $condition): self
    {
        if ($condition) {
            return $this->removeRule($name);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param bool   $condition
     * @return $this
     */
    public function removeRuleUnless(string $name, bool $condition): self
    {
        return $this->removeRuleIf($name, ! $condition);
    }

    /**
     * Remove a rule
     *
     * @param string $name
     * @return $this
     */
    public function removeRule(string $name): self
    {
        $this->items = array_filter($this->items, fn ($item) => $item !== $name);

        return $this;
    }

    /**
     * @param string $method
     * @param array  $parameters
     * @return $this
     */
    protected function handleRemoveOperations(string $method, array $parameters): self
    {
        $rule = Str::snake(str_replace(['remove', 'If', 'Unless'], '', $method));

        if (Str::endsWith($method, 'If')) {
            return $this->removeRuleIf($rule, $parameters[0]);
        }

        if (Str::endsWith($method, 'Unless')) {
            return $this->removeRuleUnless($rule, $parameters[0]);
        }

        return $this->removeRule($rule);
    }

    /**
     * Allow for rules to be added dynamically
     *
     * @param string $rule
     * @param array  $parameters
     * @return $this
     */
    public function __call($rule, $parameters)
    {
        if ($this->items instanceof MissingValue) {
            return $this;
        }

        if (Str::startsWith($rule, 'remove')) {
            return $this->handleRemoveOperations($rule, $parameters);
        }

        $rule  = Str::snake($rule);
        $count = count($parameters);

        if ($count === 1 && ! is_array($parameters[0])) {
            $rule .= ':' . $parameters[0];
        }

        if ($count === 1 && is_array($parameters[0])) {
            $rule .= ':' . implode(',', $parameters[0]);
        }

        if ($count > 1) {
            $rule .= ':' . implode(',', $parameters);
        }

        $this->items[] = $rule;

        return $this;
    }
}
