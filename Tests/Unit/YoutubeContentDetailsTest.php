<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use Carbon\VideoPlatformEditor\Infrastructure\YoutubeContentDetails;
use PHPUnit\Framework\TestCase;

class YoutubeContentDetailsTest extends TestCase
{
    /** @test */
    public function createWithISO8601duration(): void
    {
        $contentDetails = YoutubeContentDetails::fromArray([
            'duration' => 'PT21M16S'
        ]);

        self::assertEquals(
            21,
            $contentDetails->duration->i
        );
        self::assertEquals(
            16,
            $contentDetails->duration->s
        );

        self::assertEquals(
            1276,
            $contentDetails->durationAsSeconds()
        );
    }
}
