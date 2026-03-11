<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Controller;

use Behat\Transliterator\Transliterator;
use Carbon\VideoPlatformEditor\AspectRatio;
use Carbon\VideoPlatformEditor\AssetId;
use Carbon\VideoPlatformEditor\Infrastructure\ImageImporter;
use Carbon\VideoPlatformEditor\Infrastructure\OembedHtmlExtractor;
use Carbon\VideoPlatformEditor\Infrastructure\OembedMetadataProvider;
use Carbon\VideoPlatformEditor\Video;
use Carbon\VideoPlatformEditor\VideoPlatformType;
use Carbon\VideoPlatformEditor\VimeoVideoId;
use Carbon\VideoPlatformEditor\YoutubeVideoId;
use GuzzleHttp\Psr7\Response;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use Psr\Http\Message\ResponseInterface;

class VideoPlatformController extends ActionController
{
    #[Flow\Inject]
    protected PersistenceManager $persistenceManager;

    #[Flow\Inject]
    protected ImageImporter $imageImporter;

    #[Flow\Inject]
    protected OembedMetadataProvider $oembedMetadataProvider;

    #[Flow\Route('neos/carbon/video-platform/video', httpMethods: ['POST'])]
    public function videoAction(): ResponseInterface
    {
        $videoQuery = VideoQuery::fromString($this->request->getArgument('videoUri'));

        $videoUri = $videoQuery->tryToUri();

        if (!$videoUri) {
            return self::errorResponse(status: 400, message: sprintf('Not a valid uri: "%s"', $videoQuery->value));
        }

        $videoType = VideoPlatformType::tryFromHost($videoUri->getHost());
        if ($videoType === null) {
            return self::errorResponse(status: 400, message: sprintf('Video uri "%s" is not supported', $videoQuery->value));
        }

        $oembedMetadata = $this->oembedMetadataProvider->provideForVideoUri($videoType, $videoUri);
        if ($oembedMetadata === null || $oembedMetadata->type !== 'video') {
            return self::errorResponse(status: 404, message: sprintf('Video uri "%s" not found', $videoQuery->value));
        }

        if (!$oembedMetadata->html) {
            throw new \RuntimeException(sprintf('Could not determine video id for input %s, "html" missing in OembedMetadata: %s', $videoQuery->value, json_encode($oembedMetadata)), 1773254326);
        }

        $videoEmbedUri = OembedHtmlExtractor::extractVideoEmbedUri($oembedMetadata->html);

        $videoId = match ($videoType) {
            VideoPlatformType::VIMEO => VimeoVideoId::fromEmbedUri($videoEmbedUri),
            VideoPlatformType::YOUTUBE => YoutubeVideoId::fromEmbedUri($videoEmbedUri),
        };

        $thumbnailImageId = null;
        if ($oembedMetadata->thumbnailUrl) {
            // See Neos' NodeName::transliterateFromString()
            $baseFileName = Transliterator::transliterate($oembedMetadata->title);
            $baseFileName = Transliterator::urlize($baseFileName);
            $baseFileName = preg_replace('/[^a-z0-9\-]/', '', $baseFileName);

            $baseFileName = sprintf('%s-%s', strtolower($videoType->value), $baseFileName ?: $videoId->videoId);

            $thumbnailUrlsOrderedByQuality = [$oembedMetadata->thumbnailUrl];
            if ($videoId instanceof YoutubeVideoId) {
                // In case YouTube did not provide the best possible thumbnail uri and only "hqdefault" we attempt that first:
                $thumbnailUrlsOrderedByQuality = array_unique([
                    $videoId->toThumbnailUri(),
                    $oembedMetadata->thumbnailUrl
                ]);
            }

            $thumbnailImage = null;
            foreach ($thumbnailUrlsOrderedByQuality as $thumbnailUrl) {
                $thumbnailImage = $this->imageImporter->importFromUri($thumbnailUrl, $baseFileName);
                if ($thumbnailImage) {
                    break;
                }
            }

            if ($thumbnailImage) {
                // TODO Set also caption?
                if ($oembedMetadata->title) {
                    $thumbnailImage->setTitle($oembedMetadata->title);
                }
                if ($oembedMetadata->authorName) {
                    $thumbnailImage->setCopyrightNotice($oembedMetadata->authorName);
                }
                $thumbnailImageId = $this->persistenceManager->getIdentifierByObject($thumbnailImage);
            }
        }

        $video = new Video(
            id: $videoId,
            title: $oembedMetadata->title ?? '',
            aspectRatio: ($oembedMetadata->width && $oembedMetadata->height) ? AspectRatio::create(
                numerator: $oembedMetadata->width,
                denominator: $oembedMetadata->height
            ) : throw new \RuntimeException('Todo could not calculate AspectRatio', 1773255417),
            thumbnail: $thumbnailImageId !== null ? new AssetId(
                id: $thumbnailImageId
            ) : null
        );

        return new Response(
            headers: ['Content-Type' => 'application/json'],
            body: json_encode(['success' => $video])
        );
    }

    public static function errorResponse(int $status, string $message): ResponseInterface
    {
        return new Response(
            status: $status,
            headers: ['Content-Type' => 'application/json'],
            body: json_encode(['error' => [
                'message' => $message
            ]])
        );
    }
}
