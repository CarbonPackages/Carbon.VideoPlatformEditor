<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Tests\Unit;

use Carbon\VideoPlatformEditor\VimeoVideoId;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

class VimeoVideoIdTest extends TestCase
{
    /** @test */
    public function fromUri(): void
    {
        self::assertEquals(
            VimeoVideoId::create(
                videoId: '123456789',
                hash: null
            ),
            VimeoVideoId::fromEmbedUri(new Uri('https://player.vimeo.com/video/123456789?app_id=122963'))
        );
    }

    /** @test */
    public function fromUriWithHash(): void
    {
        self::assertEquals(
            VimeoVideoId::create(
                videoId: '123456789',
                hash: '3652336523'
            ),
            VimeoVideoId::fromEmbedUri(new Uri('https://player.vimeo.com/video/123456789?h=3652336523&app_id=122963'))
        );
    }

    /** @test */
    public function fromUriInvalid1(): void
    {
        $this->expectExceptionMessage('Expected vimeo oembed uri in format "player.vimeo.com/video/{id}" got "https://vimeo.com/123456789".');

        VimeoVideoId::fromEmbedUri(new Uri('https://vimeo.com/123456789'));
    }


    /** @test */
    public function fromUriInvalid2(): void
    {
        $this->expectExceptionMessage('Expected vimeo oembed uri in format "player.vimeo.com/video/{id}" got "https://vimeo.com/album/656535659/video/123456789".');

        VimeoVideoId::fromEmbedUri(new Uri('https://vimeo.com/album/656535659/video/123456789'));
    }

    /** @test */
    public function fromUriInvalid3(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        VimeoVideoId::fromEmbedUri(new Uri('https://player.vimeo.com/video/123456789/invalid'));
    }

    /** @test */
    public function fromUriInvalid4(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        VimeoVideoId::fromEmbedUri(new Uri('https://player.vimeo.com/video/invalid/123456789'));
    }

    /** @test */
    public function toUri(): void
    {
        self::assertEquals(
            'https://vimeo.com/123456789',
            VimeoVideoId::create(
                videoId: '123456789',
                hash: null
            )->toUri()->__toString()
        );
    }

    /** @test */
    public function toUriWithHash(): void
    {
        self::assertEquals(
            'https://vimeo.com/123456789/3652336523',
            VimeoVideoId::create(
                videoId: '123456789',
                hash: '3652336523'
            )->toUri()->__toString()
        );
    }
}
