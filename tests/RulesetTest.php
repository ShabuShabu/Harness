<?php

namespace ShabuShabu\Harness\Tests;

use Illuminate\Http\Resources\MissingValue;
use PHPUnit\Framework\TestCase;
use function ShabuShabu\Harness\r;

class RulesetTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_that_a_ruleset_object_can_be_initialized_with_values(): void
    {
        $ruleset = r('required', 'string');

        $this->assertEquals(['required', 'string'], $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_a_rules_can_be_set_fluently(): void
    {
        $ruleset = r()->required()->string();

        $this->assertEquals(['required', 'string'], $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_rules_can_be_completely_removed(): void
    {
        $ruleset = r()->when(false)->required()->string();

        $this->assertInstanceOf(MissingValue::class, $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_rules_can_be_added(): void
    {
        $ruleset = r();

        $this->assertEmpty($ruleset->all());

        $ruleset = $ruleset->push('required');

        $this->assertContains('required', $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_rules_do_not_get_added_for_a_condition(): void
    {
        $ruleset = r();

        $this->assertEmpty($ruleset->all());

        $ruleset = $ruleset->push('required', false);

        $this->assertNotContains('required', $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_the_sometimes_rule_can_be_removed(): void
    {
        $ruleset = r()->sometimes()->string();

        $this->assertContains('sometimes', $ruleset->all());

        $ruleset = $ruleset->removeSometimesIf(true);

        $this->assertNotContains('sometimes', $ruleset->all());
    }
}
