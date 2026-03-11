<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Infrastructure;

use Carbon\VideoPlatformEditor\VideoPlatformType;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\Utility\LogEnvironment;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;

class OembedMetadataProvider
{
    #[Flow\Inject()]
    protected LoggerInterface $logger;

    public function provideForVideoUri(VideoPlatformType $videoPlatformType, UriInterface $uri): ?OembedMetadata
    {
        $httpClient = new Client(['http_errors' => false]);

        $response = $httpClient->request('GET', $videoPlatformType->toOembedEndpoint()->withQuery(http_build_query([
            'url' => $uri->__toString(),
            'width' => 2560,
        ])));

        if ($response->getStatusCode() >= 400) {
            $this->logger->warning(sprintf('Failed to fetch oembed metadata for video "%s" of type "%s": [%d] %s', $uri, $videoPlatformType->value, $response->getStatusCode(), Message::bodySummary($response, truncateAt: 256)), LogEnvironment::fromMethodName(__METHOD__));
            return null;
        }

        $contents = $response->getBody()->getContents();

        $decodedContents = json_decode($contents, true);

        return OembedMetadata::fromArray($decodedContents);
    }
}
