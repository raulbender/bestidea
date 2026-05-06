<?php

declare(strict_types=1);

namespace Framework\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class HelpersTest extends TestCase
{
    /**
     * Test ensureInt with valid and invalid values
     */
    public function test_ensure_int(): void
    {
        $this->assertEquals(10, ensureInt(10));
        $this->assertEquals(42, ensureInt("42"));

        $this->expectException(RuntimeException::class);
        ensureInt("not-a-number");
    }

    /**
     * Test ensureString and its null variants
     */
    public function test_ensure_string(): void
    {
        $this->assertEquals("Volt", ensureString("Volt"));
        $this->assertEquals("100", ensureString(100));

        $this->assertNull(ensureStringOrNull(null));
        $this->assertEquals("Dev", ensureStringOrNull("Dev"));

        $this->expectException(RuntimeException::class);
        ensureString(null); // ensureString não aceita null
    }

    /**
     * Test ensureBool with PHP's filter logic
     */
    public function test_ensure_bool(): void
    {
        $this->assertTrue(ensureBool(true));
        $this->assertTrue(ensureBool("true"));
        $this->assertTrue(ensureBool("on"));
        $this->assertTrue(ensureBool(1));

        $this->assertFalse(ensureBool(false));
        $this->assertFalse(ensureBool("false"));
        $this->assertFalse(ensureBool("off"));
        $this->assertFalse(ensureBool(0));

        $this->expectException(RuntimeException::class);
        ensureBool("invalid-boolean-string");
    }

    /**
     * Test HTML escaping helper
     */
    public function test_html_escaping(): void
    {
        $unsafe = "<script>alert('xss')</script>";
        $safe = e($unsafe);

        $this->assertEquals("&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;", $safe);
    }

    /**
     * Test markdown-like bold converter
     */
    public function test_e_strong(): void
    {
        $text = "The ship is **strong**";
        $result = e_strong($text);

        $this->assertEquals("The ship is <strong>strong</strong>", $result);
    }
}
