<?php

declare(strict_types=1);

namespace Carbon\VideoPlatformEditor\Infrastructure;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Aop\JoinPointInterface;

#[Flow\Aspect()]
class AssetUsageExtractionAspect
{
    #[Flow\Around('method(Neos\Neos\AssetUsage\Service\AssetUsageIndexingService->extractAssetIds())')]
    public function extractAssetIdsFromValueObjects(JoinPointInterface $joinPoint): array
    {
        $nodePropertyValue = $joinPoint->getMethodArgument('value');
        if ($nodePropertyValue instanceof Video) {
            return [$nodePropertyValue->thumbnail->id];
        }
        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }
}
