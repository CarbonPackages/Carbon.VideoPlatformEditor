<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Infrastructure;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class YoutubeContentDetails
{
    private function __construct(
        public \DateInterval $duration
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        $ISO8601duration = $array['duration'] ?? throw new \InvalidArgumentException(sprintf('"duration" not specified in %s', json_encode($array)), 1773300148);

        return new self(
            duration: new \DateInterval($ISO8601duration),
        );
    }

    public function durationAsSeconds(): int
    {
        $absoluteTimeSinceEpoc = new \DateTimeImmutable('@0');
        return $absoluteTimeSinceEpoc->add($this->duration)->getTimestamp();
    }
}
