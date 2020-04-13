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
    public function ensure_that_rules_can_be_set_fluently(): void
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

        $ruleset = r()->unless(true)->required()->string();

        $this->assertInstanceOf(MissingValue::class, $ruleset->all());
    }


    /**
     * @test
     */
    public function ensure_that_a_unique_rule_can_be_added(): void
    {
        $ruleset = r()->unique('pages', 'title');

        $this->assertEquals('unique:pages,title,NULL,id', $ruleset->all()[0]);

        $ruleset = r()->unique('pages', 'title', true, 'this');

        $this->assertEquals('unique:pages,title,"this",id', $ruleset->all()[0]);
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
    public function ensure_that_a_rule_is_removed(): void
    {
        $ruleset = r()->sometimes();

        $this->assertContains('sometimes', $ruleset->all());

        $ruleset = $ruleset->removeRuleIf('sometimes', true);

        $this->assertNotContains('sometimes', $ruleset->all());

        $ruleset = r()->sometimes()->removeRuleUnless('sometimes', false);

        $this->assertNotContains('sometimes', $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_a_rule_is_not_removed(): void
    {
        $ruleset = r()->sometimes()->removeRuleIf('sometimes', false);

        $this->assertContains('sometimes', $ruleset->all());

        $ruleset = r()->sometimes()->removeRuleUnless('sometimes', true);

        $this->assertContains('sometimes', $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_a_rule_can_be_removed_dynamically(): void
    {
        $ruleset = r()->sometimes()->removeSometimesIf(true);

        $this->assertNotContains('sometimes', $ruleset->all());

        $ruleset = r()->sometimes()->removeSometimesUnless(false);

        $this->assertNotContains('sometimes', $ruleset->all());

        $ruleset = r()->sometimes()->removeSometimes();

        $this->assertNotContains('sometimes', $ruleset->all());
    }

    /**
     * @test
     */
    public function ensure_that_a_rule_is_built_properly(): void
    {
        $ruleset = r()->after('another');

        $this->assertSame('after:another', $ruleset->all()[0]);

        $ruleset = r()->endsWith(['one', 'two']);

        $this->assertSame('ends_with:one,two', $ruleset->all()[0]);

        $ruleset = r()->startsWith('one', 'two');

        $this->assertSame('starts_with:one,two', $ruleset->all()[0]);
    }
}
