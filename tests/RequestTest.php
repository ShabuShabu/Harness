<?php

namespace ShabuShabu\Harness\Tests;

use Orchestra\Testbench\TestCase;

class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
