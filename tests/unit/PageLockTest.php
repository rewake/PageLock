<?php

namespace unit;

use PHPUnit\Framework\TestCase;
use Rewake\PageLock\PageLock;

class PageLockTest extends TestCase
{
    /**
     * @test
     * @testdox Can load config json file
     */
    public function can_load_config()
    {
        // Instantiate Page Lock class
        $pl = new PageLock();

        // Load page lock config
        $lcr = $pl->loadConfig(dirname(dirname(dirname(__FILE__))) . '/resources/pageLockConfigExample.json');

        // Make sure load config result is an instance of PageLock
        $this->assertSame(get_class($lcr), PageLock::class);
    }

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

        // Pass signature on to next test
        return $s;
    }

    /**
     * @test
     * @testdox Can verify a generated signature
     * @depends can_generate_signature
     */
    public function can_verify_signature($signature)
    {
        // Instantiate Page Lock class
        $pl = new PageLock();

        // Validate the signature
        $this->assertTrue($pl->validate($signature));
    }

    /**
     * @test
     * @testdox Can verify a generated signature
     */
    public function can_verify_signature_with_custom_expire_time()
    {
        // Instantiate Page Lock class
        $pl = new PageLock();

        // Load page lock config
        $lcr = $pl->loadConfig(dirname(dirname(dirname(__FILE__))) . '/resources/pageLockConfigExample.json');

        // Generate signature
        $s = $pl->generate();

        echo "Generated signature: " . $s;

        // Make sure signature is not empty
        $this->assertNotEmpty($s);

        // Make sure signature is a string
        $this->assertTrue(is_string($s));

        // Make sure load config result is an instance of PageLock
        $this->assertSame(get_class($lcr), PageLock::class);

        // Validate the signature
        $this->assertTrue($pl->validate($s));
    }

    /**
     * @test
     * @testdox Can verify a generated signature when remote ip address is present
     */
    public function can_verify_signature_with_remote_ip()
    {
        // Set X-Forwarded-For
        $_SERVER['REMOTE_ADDR'] = '203.0.113.195';

        // Instantiate Page Lock class
        $pl = new PageLock();

        // Generate signature
        $s = $pl->generate();

        echo "Generated signature: " . $s;

        // Make sure signature is not empty
        $this->assertNotEmpty($s);

        // Make sure signature is a string
        $this->assertTrue(is_string($s));

        // Validate the signature
        $this->assertTrue($pl->validate($s));
    }

    /**
     * @test
     * @testdox Can verify a generated signature when server uses x-forward-for IPs
     */
    public function can_verify_signature_with_x_forwarded_for_ips()
    {
        // Set X-Forwarded-For
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'X-Forwarded-For: 203.0.113.195, 70.41.3.18, 150.172.238.178';

        // Instantiate Page Lock class
        $pl = new PageLock();

        // Generate signature
        $s = $pl->generate();

        echo "Generated signature: " . $s;

        // Make sure signature is not empty
        $this->assertNotEmpty($s);

        // Make sure signature is a string
        $this->assertTrue(is_string($s));

        // Validate the signature
        $this->assertTrue($pl->validate($s));
    }

    /**
     * @test
     * @testdox Can form URL with signature
     * @depends can_generate_signature
     */
    public function can_form_url($signature)
    {
        // Instantiate Page Lock class
        $pl = new PageLock();

        // Set target URL
        $targetUrl = "http://mytracker.com/page/?camp=23lkkjlk2j&xxx=123";

        // Form URL
        $redirectUrl = $pl->formUrl($targetUrl, $signature);

        // Make sure redirect URL is a valid URL
        $this->assertNotFalse(filter_var($redirectUrl, FILTER_VALIDATE_URL));

        // Make sure we can parse the URL
        $this->assertNotFalse($parsedUrl = parse_url($redirectUrl));

        // Parse query string
        parse_str($parsedUrl['query'], $qs);

        // Make sure signatures match
        $this->assertEquals($signature, $qs['s']);
    }
}