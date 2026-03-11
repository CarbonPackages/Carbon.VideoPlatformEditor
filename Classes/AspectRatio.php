<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class AspectRatio implements \JsonSerializable
{
    private function __construct(
        public string $value
    ) {
        if (preg_match('~^[1-9][0-9]* / [1-9][0-9]*$~', $this->value, $matches) !== 1) {
            throw new \InvalidArgumentException(sprintf('Invalid aspect ratio: %s', $this->value), 1773045797);
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Simplifies a fraction by dividing the numerator and denominator by their greatest common divisor.
     *
     * @param positive-int $numerator
     * @param positive-int $denominator
     */
    public static function create(int $numerator, int $denominator): ?self
    {
        if ($numerator <= 0 || $denominator <= 0) {
            throw new \InvalidArgumentException(sprintf('%d / %d is not a valid aspect ratio. Values must be positive.', $numerator, $denominator));
        }

        $divisor = self::greatestCommonDivisor($numerator, $denominator);
        $simplifiedNumerator = $numerator / $divisor;
        $simplifiedDenominator = $denominator / $divisor;

        return new self(sprintf('%d / %d', $simplifiedNumerator, $simplifiedDenominator));
    }

    /**
     * Calculates the greatest common divisor using the Euclidean algorithm.
     *
     * @param integer $numerator
     * @param integer $denominator
     * @return integer
     */
    private static function greatestCommonDivisor(int $numerator, int $denominator): int
    {
        while ($denominator != 0) {
            $temp = $denominator;
            $denominator = $numerator % $denominator;
            $numerator = $temp;
        }
        return abs($numerator); // Always return a positive GCD
    }

    /** @return array<int|string,mixed> */
    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
