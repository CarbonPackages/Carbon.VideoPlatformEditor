<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Tests\Functional;

use Carbon\VideoPlatformEditor\AspectRatio;
use Carbon\VideoPlatformEditor\AssetId;
use Carbon\VideoPlatformEditor\Video;
use Carbon\VideoPlatformEditor\YoutubeVideoId;
use Carbon\VideoPlatformEditor\YoutubeVideoType;
use Neos\ContentRepository\Core\Factory\ContentRepositoryServiceFactoryDependencies;
use Neos\ContentRepository\Core\Factory\ContentRepositoryServiceFactoryInterface;
use Neos\ContentRepository\Core\Factory\ContentRepositoryServiceInterface;
use Neos\ContentRepository\Core\Infrastructure\Property\PropertyConverter;
use Neos\ContentRepository\Core\Infrastructure\Property\PropertyType;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\SharedModel\ContentRepository\ContentRepositoryId;
use Neos\ContentRepository\Core\SharedModel\Node\PropertyName;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Tests\FunctionalTestCase;

class VideoNodePropertySerializationTest extends FunctionalTestCase
{
    protected PropertyConverter $propertyConverter;

    public function setUp(): void
    {
        parent::setUp();

        $crRegistry = $this->objectManager->get(ContentRepositoryRegistry::class);

        $spy = new class implements ContentRepositoryServiceFactoryInterface
        {
            public PropertyConverter $propertyConverter;

            public function build(ContentRepositoryServiceFactoryDependencies $serviceFactoryDependencies): ContentRepositoryServiceInterface
            {
                $this->propertyConverter = $serviceFactoryDependencies->propertyConverter;
                return new class implements ContentRepositoryServiceInterface
                {
                };
            }
        };

        $crRegistry->buildService(
            ContentRepositoryId::fromString('default'),
            $spy
        );

        $this->propertyConverter = $spy->propertyConverter;
    }

    /** @test */
    public function deAndEncodeVideoObject(): void
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

        $serialized = $this->propertyConverter->serializePropertyValue(
            PropertyType::fromNodeTypeDeclaration('Carbon\\VideoPlatformEditor\\Video', PropertyName::fromString('video'), NodeTypeName::fromString('Vendor:Test')),
            $video
        );

        self::assertEquals(
            'Carbon\\VideoPlatformEditor\\Video',
            $serialized->type
        );

        self::assertEquals(
            [
                'platformType' => 'YOUTUBE',
                'id' => [
                    'videoId' => 'a42tbe6zf',
                    'videoType' => 'VIDEO',
                ],
                'title' => 'My Title',
                'duration' => 120,
                'aspectRatio' => [
                    'value' => '16 / 9',
                ],
                'thumbnail' => [
                    'id' => '4d8c2be5-be24-4857-a2b6-c934895a0645',
                ],
            ],
            $serialized->value
        );

        self::assertEquals(
            $video,
            $this->propertyConverter->deserializePropertyValue($serialized)
        );
    }
}
