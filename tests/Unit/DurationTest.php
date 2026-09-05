<?php

namespace Tests\Unit;

use App\Support\Duration;
use InvalidArgumentException;
use Tests\TestCase;

class DurationTest extends TestCase
{
    public function test_it_converts_parts_to_minutes_and_back(): void
    {
        $total = Duration::fromParts(1, 2, 30);

        $this->assertSame(1590, $total);
        $this->assertSame([
            'days' => 1,
            'hours' => 2,
            'minutes' => 30,
        ], Duration::toParts($total));
    }

    public function test_it_formats_human_readable_duration(): void
    {
        $this->assertSame('45 minutes', Duration::format(45));
        $this->assertSame('2 hours, 15 minutes', Duration::format(135));
        $this->assertSame('1 day, 1 hour', Duration::format(1500));
    }

    public function test_it_rejects_invalid_parts(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Duration::fromParts(0, 24, 0);
    }
}
