<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Controller;

use Carbon\VideoPlatformEditor\Infrastructure\ApiService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Utility\LogEnvironment;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\Media\Domain\Model\Image;
use Neos\Media\Domain\Repository\ImageRepository;
use Neos\Utility\MediaTypes;
use Psr\Http\Message\ResponseInterface;

class VideoPlatformController extends ActionController
{
    #[Flow\Inject]
    protected PersistenceManager $persistenceManager;

    #[Flow\Inject]
    protected ResourceManager $resourceManager;

    #[Flow\Inject]
    protected ImageRepository $imageRepository;

    #[Flow\Route('neos/carbon/video-platform/video', httpMethods: ['POST'])]
    public function videoAction(): ResponseInterface
    {
        $videoId = $this->request->getArgument('id');

        $api = new ApiService();
        // TODO "width":2560,"height":1950,"duration":287,"description":"..."
        $data = $api->vimeo($videoId);
        $imageUri = $data['thumbnail_url'];
        $this->logger->debug(sprintf('Fetched thumbnail uri %s for video %s. And %s', $imageUri, $videoId, json_encode($data)), LogEnvironment::fromMethodName(__METHOD__));

        $client = new Client();
        $response = $client->request('GET', $imageUri);
        $mediaType = MediaTypes::trimMediaType(
            $response->getHeaderLine('Content-Type')
        );
        $this->logger->debug(sprintf('Thumbnail uri %s returned content-type %s (media type %s).', $imageUri, $response->getHeaderLine('Content-Type'), $mediaType), LogEnvironment::fromMethodName(__METHOD__));

        $persistentResource = $this->resourceManager->importResource(
            $response->getBody()->detach()
        );
        $persistentResource->setMediaType($mediaType);
        $persistentResource->setFilename(
            sprintf(
                'vimeo-%s.%s',
                $videoId,
                MediaTypes::getFilenameExtensionFromMediaType(
                    $mediaType
                )
            )
        );

        // Like Neos does with AssetInterfaceConverter::CONFIGURATION_ONE_PER_RESOURCE during asset upload via the media browser
        // we search for an image by the sha1 and use that if exists, the intermediate $persistentResource is cleaned up
        $poster = $this->imageRepository->findOneByResourceSha1($persistentResource->getSha1());
        if ($poster !== null) {
            $this->resourceManager->deleteResource($persistentResource);
        } else {
            $poster = new Image($persistentResource);
            $this->imageRepository->add($poster);
        }

        $posterImageId = $this->persistenceManager->getIdentifierByObject($poster);

        return new Response(
            headers: ['Content-Type' => 'application/json'],
            body: json_encode(['success' => [
                'videoTitle' => $data['title'] ?? '',
                'posterImageId' => $posterImageId
            ]])
        );
    }
}
