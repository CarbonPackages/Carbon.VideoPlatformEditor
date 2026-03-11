<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Utility\LogEnvironment;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\ImageRepository;
use Neos\Media\Domain\Repository\TagRepository;
use Neos\Utility\MediaTypes;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;

class ImageImporter
{
    #[Flow\Inject]
    protected ResourceManager $resourceManager;

    #[Flow\Inject]
    protected ImageRepository $imageRepository;

    #[Flow\Inject]
    protected TagRepository $tagRepository;

    #[Flow\Inject]
    protected LoggerInterface $logger;

    public function importFromUri(UriInterface $imageUri, string $baseFileName): ?Image
    {
        $httpClient = new Client(['http_errors' => false]);

        $response = $httpClient->request('GET', $imageUri);

        if ($response->getStatusCode() >= 400) {
            $this->logger->warning(sprintf('Failed to fetch thumbnail uri %s: [%d] %s.', $imageUri, $response->getStatusCode(), Message::bodySummary($response, truncateAt: 256)), LogEnvironment::fromMethodName(__METHOD__));
            return null;
        }

        $mediaType = MediaTypes::trimMediaType(
            $response->getHeaderLine('Content-Type')
        );

        $this->logger->debug(sprintf('Fetched thumbnail uri %s returned content-type %s.', $imageUri, $response->getHeaderLine('Content-Type')), LogEnvironment::fromMethodName(__METHOD__));

        $persistentResource = $this->resourceManager->importResource(
            $response->getBody()->detach()
        );
        $persistentResource->setMediaType($mediaType);
        $persistentResource->setFilename(
            sprintf(
                '%s.%s',
                $baseFileName,
                MediaTypes::getFilenameExtensionFromMediaType(
                    $mediaType
                )
            )
        );

        // Like Neos does with AssetInterfaceConverter::CONFIGURATION_ONE_PER_RESOURCE during asset upload via the media browser
        // we search for an image by the sha1 and use that if exists, the intermediate $persistentResource is cleaned up
        $image = $this->imageRepository->findOneByResourceSha1($persistentResource->getSha1());
        if ($image !== null) {
            $this->resourceManager->deleteResource($persistentResource);
        } else {
            $image = new Image($persistentResource);
            $image->addTag($this->getOrCreateVideoThumbnailTag());
            $this->imageRepository->add($image);
        }

        return $image;
    }

    private function getOrCreateVideoThumbnailTag(): Tag
    {
        $TAG_LABEL = 'Video Thumbnails';
        $tag = $this->tagRepository->findOneByLabel($TAG_LABEL);
        if (!$tag) {
            $tag = new Tag($TAG_LABEL);
            $this->tagRepository->add($tag);
        }
        return $tag;
    }
}
