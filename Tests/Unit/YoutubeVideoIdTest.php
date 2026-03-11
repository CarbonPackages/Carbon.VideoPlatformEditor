<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Tests\Unit;

use Carbon\VideoPlatformEditor\YoutubeVideoId;
use Carbon\VideoPlatformEditor\YoutubeVideoType;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class YoutubeVideoIdTest extends TestCase
{
    /** @test */
    public function fromUri(): void
    {
        self::assertEquals(
            YoutubeVideoId::create(
                videoId: 'war4567fweF',
                videoType: YoutubeVideoType::VIDEO
            ),
            YoutubeVideoId::fromEmbedUri(new Uri('https://www.youtube.com/embed/war4567fweF?feature=oembed'))
        );
    }

    /** @test */
    public function fromUriInvalid1(): void
    {
        $this->expectExceptionMessage('Expected vimeo oembed uri in format "www.youtube.com/embed/{id}" got "https://www.youtube.com/watch?v=war4567fweF".');

        YoutubeVideoId::fromEmbedUri(new Uri('https://www.youtube.com/watch?v=war4567fweF'));
    }


    /** @test */
    public function fromUriInvalid2(): void
    {
        $this->expectExceptionMessage('Expected vimeo oembed uri in format "www.youtube.com/embed/{id}" got "https://www.youtube.com/war4567fweF".');

        YoutubeVideoId::fromEmbedUri(new Uri('https://www.youtube.com/war4567fweF'));
    }

    /** @test */
    public function fromUriInvalid3(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        YoutubeVideoId::fromEmbedUri(new Uri('https://www.youtube.com/invalid/embed/war4567fweF?feature=oembed'));
    }

    /** @test */
    public function fromUriInvalid4(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        YoutubeVideoId::fromEmbedUri(new Uri('https://www.youtube.com/invalid/embed/war4567fweF/invalid?feature=oembed'));
    }

    /** @test */
    public function fromUriInvalid5(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // missing www.
        YoutubeVideoId::fromEmbedUri(new Uri('https://youtube.com/embed/war4567fweF'));
    }

    /** @test */
    public function toUri(): void
    {
        self::assertEquals(
            'https://www.youtube.com/watch?v=war4567fweF',
            YoutubeVideoId::create(
                videoId: 'war4567fweF',
                videoType: YoutubeVideoType::VIDEO
            )->toUri()->__toString()
        );
    }
}
