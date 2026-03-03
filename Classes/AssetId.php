<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class AssetId
{
    public function __construct(
        public string $id
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            id: $array['id'],
        );
    }
}
