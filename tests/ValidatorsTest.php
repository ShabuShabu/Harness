<?php

namespace ShabuShabu\Harness\Tests;

use Orchestra\Testbench\TestCase;
use ShabuShabu\Harness\Validators\{ValidateLatitude, ValidateLongitude};

class ValidatorsTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_that_a_valid_latitude_passes_validation(): void
    {
        $validator = new ValidateLatitude();

        $this->assertTrue($validator->validate(null, 0));
        $this->assertTrue($validator->validate(null, 90));
        $this->assertTrue($validator->validate(null, -90));
        $this->assertTrue($validator->validate(null, 48.307267));
        $this->assertTrue($validator->validate(null, -48.307267));
    }

    /**
     * @test
     */
    public function ensure_that_an_invalid_latitude_does_not_pass_validation(): void
    {
        $validator = new ValidateLatitude();

        $this->assertFalse($validator->validate(null, 'test'));
        $this->assertFalse($validator->validate(null, null));
        $this->assertFalse($validator->validate(null, 90.01));
        $this->assertFalse($validator->validate(null, -90.01));
    }

    /**
     * @test -180 and +180
     */
    public function ensure_that_a_valid_longitude_passes_validation(): void
    {
        $validator = new ValidateLongitude();

        $this->assertTrue($validator->validate(null, 0));
        $this->assertTrue($validator->validate(null, 180));
        $this->assertTrue($validator->validate(null, -180));
        $this->assertTrue($validator->validate(null, 11.907494));
        $this->assertTrue($validator->validate(null, -11.907494));
    }

    /**
     * @test
     */
    public function ensure_that_an_invalid_longitude_does_not_pass_validation(): void
    {
        $validator = new ValidateLongitude();

        $this->assertFalse($validator->validate(null, 'test'));
        $this->assertFalse($validator->validate(null, null));
        $this->assertFalse($validator->validate(null, 180.01));
        $this->assertFalse($validator->validate(null, -180.01));
    }
}
