<?php

namespace Tests\Unit;

use App\Services\SeoAnalyzer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeoAnalyzerTest extends TestCase
{
    #[Test]
    public function it_scores_complete_seo_fields_highly(): void
    {
        $analyzer = new SeoAnalyzer;

        $result = $analyzer->scorePage('home', 'Homepage', [
            'title' => 'Steam Car Wash Punjab | Diamond Steam Sector 66',
            'description' => 'Book paint-safe steam car wash, detailing, ceramic coating, and PPF in Sector 66, SAS Nagar and Matour, Punjab today.',
            'content' => str_repeat('Steam wash detailing ceramic coating Punjab Matour Sector 66 paint safe care. ', 40),
            'has_image' => true,
            'has_h1' => true,
            'canonical' => 'https://example.test/',
            'og_title' => 'Steam Car Wash Punjab | Diamond Steam Sector 66',
            'og_description' => 'Book paint-safe steam car wash, detailing, ceramic coating, and PPF in Sector 66, SAS Nagar and Matour, Punjab today.',
        ]);

        $this->assertGreaterThanOrEqual(80, $result['score']);
        $this->assertNotEmpty($result['suggestions']);
    }

    #[Test]
    public function it_flags_missing_meta_fields(): void
    {
        $analyzer = new SeoAnalyzer;

        $result = $analyzer->scorePage('thin', 'Thin Page', [
            'title' => '',
            'description' => '',
            'content' => 'Short',
            'has_image' => false,
            'has_h1' => false,
            'canonical' => '',
            'og_title' => '',
            'og_description' => '',
        ]);

        $this->assertLessThan(40, $result['score']);
        $this->assertTrue(collect($result['suggestions'])->contains(fn ($s) => str_contains($s, 'meta title')));
    }
}
