<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Tests\Functional;

use Carbon\VideoPlatformEditor\Infrastructure\OembedMetadataProvider;
use Carbon\VideoPlatformEditor\VideoPlatformType;
use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Tests\FunctionalTestCase;

class OembedMetadataProviderTest extends FunctionalTestCase
{
    public function setUp(): void
    {
        if (getenv('CI')) {
            $this->markTestSkipped('Not running in CI pipeline, test is for developers');
        }
    }

    /**
     * @test
     */
    public function youtubeOembedApi()
    {
        $subject = new OembedMetadataProvider();
        $metadata = $subject->provideForVideoUri(VideoPlatformType::YOUTUBE, new Uri('https://www.youtube.com/watch?v=warC3CxMtOE'));

        self::assertNotNull($metadata);
        self::assertEquals('video', $metadata->type);
        self::assertEquals('7 unzerstörbare Motorräder', $metadata->title);
        self::assertEquals('ChainBrothers', $metadata->authorName);
        self::assertEquals(200, $metadata->width);
        self::assertEquals(113, $metadata->height);
        self::assertEquals('https://i.ytimg.com/vi/warC3CxMtOE/hqdefault.jpg', $metadata->thumbnailUrl);
        self::assertNull($metadata->tryGetNonStandardDuration());
    }

    /**
     * @test
     */
    public function vimeoOembedApi()
    {
        $subject = new OembedMetadataProvider();
        $metadata = $subject->provideForVideoUri(VideoPlatformType::VIMEO, new Uri('https://vimeo.com/544027166'));

        self::assertNotNull($metadata);
        self::assertEquals('video', $metadata->type);
        self::assertEquals('Plausible integration for Neos CMS Trailer', $metadata->title);
        self::assertEquals('Jonnitto', $metadata->authorName);
        self::assertEquals(2560, $metadata->width);
        self::assertEquals(1440, $metadata->height);
        self::assertEquals('https://i.vimeocdn.com/video/1126278547-62d60cb97656c3ca4efe48084e3f032850596215a2ff9453e463267dae43b6ec-d_1280?region=us', $metadata->thumbnailUrl);
        self::assertEquals(130, $metadata->tryGetNonStandardDuration());
    }
}
