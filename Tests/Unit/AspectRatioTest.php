<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Tests\Unit;

use Carbon\VideoPlatformEditor\AspectRatio;
use PHPUnit\Framework\TestCase;

class AspectRatioTest extends TestCase
{
    /** @test */
    public function fromString(): void
    {
        self::assertEquals(
            '16 / 9',
            AspectRatio::fromString('16 / 9')->value
        );
    }

    /** @test */
    public function fromStringInvalid1(): void
    {
        $this->expectExceptionMessage('Invalid aspect ratio: 0 / 9');
        AspectRatio::fromString('0 / 9');
    }

    /** @test */
    public function fromStringInvalid2(): void
    {
        $this->expectExceptionMessage('Invalid aspect ratio: 1.6');
        AspectRatio::fromString('1.6');
    }

    /** @test */
    public function fromStringInvalid3(): void
    {
        $this->expectExceptionMessage('Invalid aspect ratio: 1/2');
        AspectRatio::fromString('1/2');
    }

    /** @test */
    public function createAspectRatio(): void
    {
        self::assertEquals(
            '16 / 9',
            AspectRatio::create(
                numerator: 16,
                denominator: 9
            )->value
        );
    }

    /** @test */
    public function calculateAspectRatio(): void
    {
        self::assertEquals(
            '16 / 9',
            AspectRatio::create(
                numerator: 1600,
                denominator: 900
            )->value
        );
    }

    /** @test */
    public function invalidForZeroDenominator(): void
    {
        $this->expectExceptionMessage('100 / 0 is not a valid aspect ratio. Values must be positive.');
        AspectRatio::create(
            numerator: 100,
            denominator: 0
        );
    }

    /** @test */
    public function invalidForZeroNumerator(): void
    {
        $this->expectExceptionMessage('0 / 100 is not a valid aspect ratio. Values must be positive.');
        AspectRatio::create(
            numerator: 0,
            denominator: 100
        );
    }

    /** @test */
    public function invalidForNegativeDenominator(): void
    {
        $this->expectExceptionMessage('100 / -100 is not a valid aspect ratio. Values must be positive.');
        AspectRatio::create(
            numerator: 100,
            denominator: -100
        );
    }

    /** @test */
    public function invalidForNegativeNumerator(): void
    {
        $this->expectExceptionMessage('-100 / 100 is not a valid aspect ratio. Values must be positive.');
        AspectRatio::create(
            numerator: -100,
            denominator: 100
        );
    }
}
