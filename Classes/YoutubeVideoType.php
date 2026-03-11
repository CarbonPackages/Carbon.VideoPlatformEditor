<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

enum YoutubeVideoType: string implements \JsonSerializable
{
    case VIDEO = 'VIDEO';
    // possibly also to support:
    // case SHORT = 'SHORT';
    // case PLAYLIST = 'PLAYLIST';

    public function jsonSerialize(): string
    {
        /** Workaround for {@see https://github.com/neos/neos-ui/pull/4092} */
        return $this->value;
    }
}
