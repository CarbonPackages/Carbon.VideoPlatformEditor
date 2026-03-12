<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Tests\Unit;

use Carbon\VideoPlatformEditor\AspectRatio;
use Carbon\VideoPlatformEditor\AssetId;
use Carbon\VideoPlatformEditor\Video;
use Carbon\VideoPlatformEditor\YoutubeVideoId;
use Carbon\VideoPlatformEditor\YoutubeVideoType;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase
{
    /** @test */
    public function jsonDeAndEncode(): void
    {
        $video = new Video(
            id: YoutubeVideoId::create(videoId: 'a42tbe6zf', videoType: YoutubeVideoType::VIDEO),
            title: 'My Title',
            duration: 120,
            aspectRatio: AspectRatio::create(16, 9),
            thumbnail: new AssetId(
                id: '4d8c2be5-be24-4857-a2b6-c934895a0645'
            )
        );

        $json = <<<'JSON'
        {
            "platformType": "YOUTUBE",
            "id": {
                "videoId": "a42tbe6zf",
                "videoType": "VIDEO"
            },
            "title": "My Title",
            "duration": 120,
            "aspectRatio": "16 / 9",
            "thumbnail": {
                "id": "4d8c2be5-be24-4857-a2b6-c934895a0645"
            },
            "uri": "https://www.youtube.com/watch?v=a42tbe6zf"
        }
        JSON;

        self::assertJsonStringEqualsJsonString(
            $json,
            json_encode($video)
        );

        self::assertEquals(
            $video,
            Video::fromArray(json_decode($json, true))
        );
    }
}
