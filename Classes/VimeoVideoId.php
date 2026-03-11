<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Annotations as Flow;
use Psr\Http\Message\UriInterface;

#[Flow\Proxy(false)]
final readonly class VimeoVideoId
{
    private function __construct(
        public string $videoId,
        public string $hash
    ) {
        if ($this->videoId === '') {
            throw new \InvalidArgumentException('Vimeo video id must not be empty.', 1773251720);
        }
    }

    public static function create(
        string $videoId,
        null|string $hash,
    ): self {
        return new self(
            videoId: $videoId,
            hash: $hash ?? '',
        );
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            videoId: $array['videoId'],
            hash: $array['hash'] ?? '',
        );
    }

    public static function fromEmbedUri(UriInterface $uri): self
    {
        if ($uri->getHost() !== 'player.vimeo.com' || preg_match('~^/video/(?<id>[^/]+)$~', $uri->getPath(), $matches) !== 1) {
            throw new \InvalidArgumentException(sprintf('Expected vimeo oembed uri in format "player.vimeo.com/video/{id}" got "%s".', $uri->__toString()), 1773247104);
        }
        $queryParametersFromUri = [];
        if ($uri->getQuery() !== '') {
            parse_str($uri->getQuery(), $queryParametersFromUri);
        }
        return new self(
            videoId: $matches['id'],
            hash: $queryParametersFromUri['h'] ?? '',
        );
    }

    public function toUri(): UriInterface
    {
        return new Uri(sprintf('https://vimeo.com/%s%s', $this->videoId, $this->hash === '' ? '' : ('/' . $this->hash)));
    }
}
