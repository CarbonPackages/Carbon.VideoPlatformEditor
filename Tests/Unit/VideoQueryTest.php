<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Tests\Unit;

use Carbon\VideoPlatformEditor\Controller\VideoQuery;
use PHPUnit\Framework\TestCase;

class VideoQueryTest extends TestCase
{
    /** @test */
    public function toUri(): void
    {
        $uri = VideoQuery::fromString('https://youtube.com/watch?v=fwefewgweg')->tryToUri();

        self::assertNotNull($uri);
        self::assertEquals('https', $uri->getScheme());
        self::assertEquals('youtube.com', $uri->getHost());
        self::assertEquals('/watch', $uri->getPath());

        self::assertEquals(
            'https://youtube.com/watch?v=fwefewgweg',
            (string)$uri
        );
    }

    /** @test */
    public function toUriWithoutProtocol(): void
    {
        $uri = VideoQuery::fromString('youtube.com/watch?v=fwefewgweg')->tryToUri();

        self::assertNotNull($uri);
        self::assertEquals('', $uri->getScheme());
        self::assertEquals('youtube.com', $uri->getHost());
        self::assertEquals('/watch', $uri->getPath());

        self::assertEquals(
            '//youtube.com/watch?v=fwefewgweg',
            (string)$uri
        );
    }

    /** @test */
    public function toUriInvalid1(): void
    {
        self::assertNull(
            VideoQuery::fromString('')->tryToUri()
        );
    }

    /** @test */
    public function toUriInvalid2(): void
    {
        self::assertNull(
            VideoQuery::fromString('@')->tryToUri()
        );
    }
}
