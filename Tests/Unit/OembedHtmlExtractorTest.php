<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use Carbon\VideoPlatformEditor\Infrastructure\OembedHtmlExtractor;
use PHPUnit\Framework\TestCase;

class OembedHtmlExtractorTest extends TestCase
{
    /** @test */
    public function extractVideoEmbedUri()
    {
        self::assertEquals(
            'https://www.youtube.com/embed/456784981?feature=oembed',
            OembedHtmlExtractor::extractVideoEmbedUri(<<<'HTML'
            <iframe width="113" height="200" src="https://www.youtube.com/embed/456784981?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen title="Some title"></iframe>
            HTML)->__toString()
        );
    }

    /** @test */
    public function extractVideoEmbedUriInvalid1()
    {
        $this->expectExceptionMessage('HTML does not specify iframe with source: ""');

        OembedHtmlExtractor::extractVideoEmbedUri(<<<'HTML'
        
        HTML);
    }

    /** @test */
    public function extractVideoEmbedUriInvalid2()
    {
        $this->expectExceptionMessage('HTML does not specify iframe with source: "<div></div>"');

        OembedHtmlExtractor::extractVideoEmbedUri(<<<'HTML'
        <div></div>
        HTML);
    }

    /** @test */
    public function extractVideoEmbedUriInvalid3()
    {
        $this->expectExceptionMessage('HTML does not specify iframe with source: "<iframe></iframe>"');

        OembedHtmlExtractor::extractVideoEmbedUri(<<<'HTML'
        <iframe></iframe>
        HTML);
    }
}
