<?php

namespace unit;

use PHPUnit\Framework\TestCase;
use Rewake\PageLock\PageLock;

class PageLockTest extends TestCase
{
    /**
     * @test
     * @testdox Can generate a Page Lock signature
     */
    public function can_generate_signature()
    {
        // Instantiate Page Lock class
        $pl = new PageLock();

        // Generate signature
        $s = $pl->generate();

        echo "Generated signature: " . $s;

        // Make sure signature is not empty
        $this->assertNotEmpty($s);

        // Make sure signature is a string
        $this->assertTrue(is_string($s));
    }
}