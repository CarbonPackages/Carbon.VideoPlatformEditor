<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Infrastructure;

use GuzzleHttp\Psr7\Uri;
use Neos\Flow\Annotations as Flow;
use Psr\Http\Message\UriInterface;

#[Flow\Proxy(false)]
final class OembedHtmlExtractor
{
    private function __construct()
    {
    }

    public static function extractVideoEmbedUri(string $html): UriInterface
    {
        $dom = new \DOMDocument();
        if ($html !== '') {
            $dom->loadHTML($html);
        }
        $iframe = $dom->getElementsByTagName('iframe')->item(0);
        $source = $iframe?->getAttribute('src');
        if (!$source) {
            throw new \RuntimeException(sprintf('HTML does not specify iframe with source: "%s"', $html), 1772985314);
        }
        return new Uri($source);
    }
}
