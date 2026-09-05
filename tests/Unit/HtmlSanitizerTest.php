<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_allows_safe_formatting_and_images(): void
    {
        $html = '<p>Hello <strong>world</strong></p><p><img src="/storage/editor/a.jpg" alt="Wash"></p>';

        $result = app(HtmlSanitizer::class)->sanitize($html);

        $this->assertStringContainsString('<strong>world</strong>', $result);
        $this->assertStringContainsString('src="/storage/editor/a.jpg"', $result);
    }

    public function test_it_strips_scripts_and_event_handlers(): void
    {
        $html = '<p onclick="alert(1)">Safe</p><script>alert(1)</script><a href="javascript:alert(1)">x</a>';

        $result = app(HtmlSanitizer::class)->sanitize($html);

        $this->assertStringNotContainsString('<script', $result);
        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringNotContainsString('javascript:', $result);
        $this->assertStringContainsString('Safe', $result);
    }
}
