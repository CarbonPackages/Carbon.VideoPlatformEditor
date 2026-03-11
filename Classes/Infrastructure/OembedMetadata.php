<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Infrastructure;

use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Annotations as Flow;
use Psr\Http\Message\UriInterface;

/**
 * For specification {@link https://oembed.com}
 */
#[Flow\Proxy(false)]
final readonly class OembedMetadata
{
    public function __construct(
        public string $version,
        public string $type,
        public ?string $title,
        public ?string $authorName,
        public ?int $height,
        public ?int $width,
        public ?UriInterface $thumbnailUrl,
        public ?string $html,
        /**
         * Non standard properties:
         */
        public ?int $duration,
    ) {
        if ($this->version !== '1.0') {
            throw new \RuntimeException(sprintf('Oembed version must be 1.0 got %s', $this->version), 1773254133);
        }
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            version: $array['version'] ?? throw new \RuntimeException(sprintf('Field "version" is required for oembed response: %s', json_encode($array)), 1773254023),
            type: $array['type'] ?? throw new \RuntimeException(sprintf('Field "type" is required for oembed response: %s', json_encode($array)), 1773254061),
            title: $array['title'] ?? null,
            authorName: $array['author_name'] ?? null,
            height: $array['height'] ?? null,
            width: $array['width'] ?? null,
            thumbnailUrl: isset($array['thumbnail_url']) ? new Uri($array['thumbnail_url']) : null,
            html: $array['html'] ?? null,
            duration: $array['duration'] ?? null,
        );
    }
}
