<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Controller;

use GuzzleHttp\Psr7\Exception\MalformedUriException;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final class VideoQuery
{
    private function __construct(public string $value)
    {
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function tryToUri(): ?UriInterface
    {
        if ($this->value === '') {
            return null;
        }

        $value = $this->value;

        if (parse_url($this->value, PHP_URL_SCHEME) === null) {
            $value = sprintf('//%s', $value);
        }

        try {
            return new Uri($value);
        } catch (MalformedUriException) {
            return null;
        }
    }
}
