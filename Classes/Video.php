<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class Video implements \JsonSerializable
{
    public VideoPlatformType $platformType;

    public function __construct(
        public VimeoVideoId|YoutubeVideoId $id,
        public string $title,
        public AspectRatio $aspectRatio,
        public ?AssetId $thumbnail,
    ) {
        $this->platformType = match ($this->id::class) {
            VimeoVideoId::class => VideoPlatformType::VIMEO,
            YoutubeVideoId::class => VideoPlatformType::YOUTUBE,
        };
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            id: match (VideoPlatformType::from($array['platformType'])) {
                VideoPlatformType::VIMEO => VimeoVideoId::fromArray($array['id']),
                VideoPlatformType::YOUTUBE => YoutubeVideoId::fromArray($array['id']),
            },
            title: $array['title'],
            aspectRatio: is_string($array['aspectRatio']) ? AspectRatio::fromString($array['aspectRatio']) : AspectRatio::fromString($array['aspectRatio']['value']),
            thumbnail: isset($array['thumbnail']) ? AssetId::fromArray($array['thumbnail']) : null,
        );
    }

    /** @return array<int|string,mixed> */
    public function jsonSerialize(): mixed
    {
        return [
            'platformType' => $this->platformType,
            'id' => $this->id,
            'title' => $this->title,
            'aspectRatio' => $this->aspectRatio,
            'thumbnail' => $this->thumbnail,
            // Uri is added to serialisation for presentation
            'uri' => $this->id->toUri()
        ];
    }
}
