<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Annotations as Flow;
use Psr\Http\Message\UriInterface;

#[Flow\Proxy(false)]
final readonly class YoutubeVideoId
{
    private function __construct(
        public string $videoId,
        public YoutubeVideoType $videoType
    ) {
        if ($this->videoId === '') {
            throw new \InvalidArgumentException('Youtube video id must not be empty.', 1773251877);
        }
    }

    public static function create(
        string $videoId,
        YoutubeVideoType $videoType,
    ): self {
        return new self(
            videoId: $videoId,
            videoType: $videoType,
        );
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            videoId: $array['videoId'],
            videoType: YoutubeVideoType::from($array['videoType']),
        );
    }

    public static function fromEmbedUri(UriInterface $uri): self
    {
        if ($uri->getHost() !== 'www.youtube.com' || preg_match('~^/embed/(?<id>[^/]+)$~', $uri->getPath(), $matches) !== 1) {
            throw new \InvalidArgumentException(sprintf('Expected vimeo oembed uri in format "www.youtube.com/embed/{id}" got "%s".', $uri->__toString()), 1773251937);
        }
        return new self(
            videoId: $matches['id'],
            videoType: YoutubeVideoType::VIDEO
        );
    }

    public function toThumbnailUri(): UriInterface
    {
        return new Uri(sprintf('https://i.ytimg.com/vi/%s/maxresdefault.jpg', $this->videoId));
    }

    public function toUri(): UriInterface
    {
        return (new Uri('https://www.youtube.com/watch'))->withQuery(
            http_build_query([
                'v' => $this->videoId
            ])
        );
    }
}
