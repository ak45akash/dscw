<?php

namespace App\Services;

class MediaDisk
{
    public static function name(): string
    {
        return (string) config('dscw.media_disk', 'public');
    }
}
