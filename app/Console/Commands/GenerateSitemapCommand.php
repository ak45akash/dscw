<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate and cache the public sitemap.xml';

    public function handle(SitemapService $sitemap): int
    {
        $sitemap->writeCachedFile();
        $this->info('Sitemap generated ('.count($sitemap->urls()).' URLs).');

        return self::SUCCESS;
    }
}
