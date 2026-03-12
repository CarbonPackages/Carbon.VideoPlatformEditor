<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Infrastructure;

use Carbon\VideoPlatformEditor\YoutubeVideoId;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Utility\LogEnvironment;
use Psr\Log\LoggerInterface;

class YoutubeContentDetailsProvider
{
    #[Flow\InjectConfiguration(path: 'youtubeApiKey', package: 'Carbon.VideoPlatformEditor')]
    protected ?string $apiKey;

    #[Flow\Inject()]
    protected LoggerInterface $logger;

    public function provideForVideoId(YoutubeVideoId $videoId): ?YoutubeContentDetails
    {
        $httpClient = new Client(['http_errors' => false]);

        $uri = new Uri('https://www.googleapis.com/youtube/v3/videos');

        if (empty($this->apiKey)) {
            return null;
        }

        $response = $httpClient->request('GET', $uri->withQuery(http_build_query([
            'key' => $this->apiKey,
            'part' => 'contentDetails',
            'id' => $videoId->videoId
        ])));

        if ($response->getStatusCode() >= 400) {
            $this->logger->warning(sprintf('Failed to fetch google api for video "%s": [%d] %s', $videoId->videoId, $response->getStatusCode(), Message::bodySummary($response, truncateAt: 256)), LogEnvironment::fromMethodName(__METHOD__));
            return null;
        }

        $contents = $response->getBody()->getContents();

        $decodedContents = json_decode($contents, true);

        $firstItem = $decodedContents['items'][0] ?? null;
        $firstItemVideoId = $firstItem['id'] ?? null;

        if ($firstItemVideoId !== $videoId->videoId) {
            $this->logger->warning(sprintf('Google api did not return contentDetails for video "%s" but for video: %s', $videoId->videoId, json_encode($firstItem)), LogEnvironment::fromMethodName(__METHOD__));
            return null;
        }

        $contentDetails = $firstItem['contentDetails'] ?? null;
        if (!is_array($contentDetails)) {
            $this->logger->warning(sprintf('Google api did not return contentDetails for video "%s" got: %s', $videoId->videoId, $contents), LogEnvironment::fromMethodName(__METHOD__));
            return null;
        }

        return YoutubeContentDetails::fromArray($contentDetails);
    }
}
