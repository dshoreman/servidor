<?php

namespace Tests\Unit;

use Servidor\Rules\Domain;
use Tests\TestCase;
use Validator;

class DomainValidationTest extends TestCase
{
    /**
     * @var array
     */
    private $rules;

    /**
     * @var Validator
     */
    private $validator;

    public function setUp(): void
    {
        parent::setUp();

        $this->rules = ['domain' => [new Domain]];
        $this->validator = $this->app['validator'];
    }

    /** @test */
    public function validates_short_hostnames(): void
    {
        $this->assertTrue($this->validate('localhost'));
    }

    /** @test */
    public function validates_hostnames_with_dashes(): void
    {
        $this->assertTrue($this->validate('example-hostname-'));
    }

    /** @test */
    public function validates_hostnames_with_digits(): void
    {
        $this->assertTrue($this->validate('hostname-123'));
    }

    /** @test */
    public function validates_domains_with_common_tlds(): void
    {
        $this->assertTrue($this->validate('localhost.localdomain'));
        $this->assertTrue($this->validate('example.com'));
        $this->assertTrue($this->validate('example.co'));
        $this->assertTrue($this->validate('example.co.uk'));
        $this->assertTrue($this->validate('example.dev'));
    }

    /** @test */
    public function validates_fqdn_with_many_subdomains(): void
    {
        $this->assertTrue($this->validate('ab.cd.ef.gh.ij.kl.mn.o.pq.rs.tuv.wx.yz'));
    }

    /**
     * @test
     * RFC 2181, section 11: DNS labels can consist of any symbols
     */
    public function validates_domains_with_symbols(): void
    {
        $this->assertTrue($this->validate('ec2-35-160-210-253.us-west-2-.compute.amazonaws.com'));
        $this->assertTrue($this->validate('-ec2_35$160%210-253.us-west-2-.compute.amazonaws.com'));
    }

    /**
     * @test
     * RFC 1123, section 2.1: A segment may begin with a degit and could be entirely numeric
     */
    public function validates_digit_segments(): void
    {
        $this->assertTrue($this->validate('1example.com'));
        $this->assertTrue($this->validate('123.456.789.com'));
    }

    /** @test */
    public function validates_fqdn_with_root_zone(): void
    {
        $this->assertTrue($this->validate('mx.example.com.'));
    }

    /** @test */
    public function validates_internationalised_punycode_domains(): void
    {
        $this->assertTrue($this->validate('xn--d1aacihrobi6i.xn--p1ai'));
        $this->assertTrue($this->validate('xn--kxae4bafwg.xn--pxaix.gr'));
    }

    /** @test */
    public function rejects_domains_that_include_protocol(): void
    {
        $this->assertFalse($this->validate('http://example.com'));
        $this->assertFalse($this->validate('https://example.com'));
        $this->assertFalse($this->validate('https://localhost'));
    }

    /**
     * @test
     * HOSTNAME(7): A hostname may not start with a hyphen
     */
    public function rejects_hostname_starting_with_dash(): void
    {
        $this->assertFalse($this->validate('-example'));
    }

    /**
     * @test
     * RFC 3696, section 2: Requires that top-level domains not be all-numeric
     */
    public function rejects_numeric_tld(): void
    {
        $this->assertFalse($this->validate('sub-domain.example.123'));
    }

    /**
     * @test
     * RFC 5890, section 2.3.1: Length of DNS labels must not exceed 63 octets
     */
    public function rejects_segments_exceeding_63_chars(): void
    {
        $this->assertFalse($this->validate('1234567890-1234567890-1234567890-1234567890-12345678901234567890.example.com'));
        $this->assertFalse($this->validate('1234567890-1234567890-1234567890-1234567890-12345678901234567890'));
    }

    /**
     * @test
     * RFC 1035, section 3.1: Length of an FQDN is limited to 255 characters
     * HOSTNAME(7): the entire hostname, including the dots, can be at most 253 characters long.
     */
    public function rejects_fqdn_exceeding_253_chars(): void
    {
        $this->assertFalse($this->validate('1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.1234567890.12345678.90.example.com'));
    }

    private function validate($value)
    {
        return $this->getValidator($value)->passes();
    }

    private function getValidator($value)
    {
        return $this->validator->make(
            ['domain' => $value],
            $this->rules,
        );
    }
}
