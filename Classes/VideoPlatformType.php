<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

enum VideoPlatformType: string implements \JsonSerializable
{
    case YOUTUBE = 'YOUTUBE';
    case VIMEO = 'VIMEO';

    public static function tryFromHost(string $hostname): ?self
    {
        $dnsHierarchy = array_reverse(explode('.', $hostname));
        $topLevelDomain = $dnsHierarchy[0] ?? null;
        $secondLevelDomain = $dnsHierarchy[1] ?? null;

        return match ($topLevelDomain) {
            'com' => match ($secondLevelDomain) {
                'vimeo' => self::VIMEO,
                'youtube' => self::YOUTUBE,
                default => null
            },
            'be' => match ($secondLevelDomain) {
                'youtu' => self::YOUTUBE,
                default => null
            },
            default => null
        };
    }

    public function toOembedEndpoint(): UriInterface
    {
        return match ($this) {
            self::YOUTUBE => new Uri('https://www.youtube.com/oembed'),
            self::VIMEO => new Uri('https://vimeo.com/api/oembed.json'),
        };
    }

    public function jsonSerialize(): string
    {
        /** Workaround for {@see https://github.com/neos/neos-ui/pull/4092} */
        return $this->value;
    }
}
