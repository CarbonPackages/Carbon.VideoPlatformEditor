<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class Video implements \JsonSerializable
{
    public function __construct(
        public string $id,
        public VideoPlatformType $platformType,
        public string $title,
        public ?AssetId $poster,
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            id: $array['id'],
            platformType: VideoPlatformType::from($array['platformType']),
            title: $array['title'],
            poster: isset($array['poster']) ? AssetId::fromArray($array['poster']) : null,
        );
    }

    /** @return array<int|string,mixed> */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'platformType' => $this->platformType,
            'title' => $this->title,
            'poster' => $this->poster,
        ];
    }
}
