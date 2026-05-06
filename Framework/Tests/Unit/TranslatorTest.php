<?php

declare(strict_types=1);

namespace Framework\Tests\Unit;

use Framework\Http\Request;
use Framework\Utils\Translator;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /**
     * Test if the translator correctly detects Portuguese from headers
     */
    public function test_it_detects_portuguese_language_from_request(): void
    {
        $request = $this->createMock(Request::class);

        // Fix: Use expects() to define the mock behavior properly
        $request->expects($this->any())
                ->method('getHeader')
                ->with('Accept-Language')
                ->willReturn('pt-BR,pt;q=0.9');

        $translator = new Translator($request);

        $this->assertEquals('pt', $translator->language());
    }

    /**
     * Test if it falls back to English when language is not supported
     */
    public function test_it_falls_back_to_english_for_unsupported_languages(): void
    {
        $request = $this->createMock(Request::class);

        $request->expects($this->any())
                ->method('getHeader')
                ->with('Accept-Language')
                ->willReturn('fr-FR,fr;q=0.8');

        $translator = new Translator($request);

        $this->assertEquals('en', $translator->language());
    }

    /**
     * Test retrieval of a simple nested translation key
     */
    public function test_it_can_retrieve_nested_translation_keys(): void
    {
        $request = $this->createMock(Request::class);
        $request->expects($this->any())
                ->method('getHeader')
                ->willReturn('en-US');

        $translator = new Translator($request);

        // Accessing 'layout.nav_brand' inside en.php
        $this->assertEquals('⚡Volt R²', $translator->get('layout.nav_brand'));
    }

    /**
     * Test behavior when a translation key does not exist
     */
    public function test_it_returns_the_key_itself_if_translation_not_found(): void
    {
        $request = $this->createMock(Request::class);
        $translator = new Translator($request);

        $this->assertEquals('non.existent.key', $translator->get('non.existent.key'));
    }

    /**
     * Test if it returns the key when the path is incomplete (not a string)
     */
    public function test_it_returns_key_if_path_points_to_an_array(): void
    {
        $request = $this->createMock(Request::class);
        $translator = new Translator($request);

        // 'layout' is an array, not a leaf string. Should return the key name.
        $this->assertEquals('layout', $translator->get('layout'));
    }
}
