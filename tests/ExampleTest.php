<?php

namespace ShabuShabu\Harness\Tests;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
