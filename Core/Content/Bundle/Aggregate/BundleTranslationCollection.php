<?php declare(strict_types=1);

namespace SwagBundleManager\Core\Content\Bundle\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class BundleTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return BundleTranslationEntity::class;
    }
}