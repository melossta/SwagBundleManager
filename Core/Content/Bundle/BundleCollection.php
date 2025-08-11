<?php declare(strict_types=1);

namespace SwagBundleManager\Core\Content\Bundle;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Swag\BundleManager\Core\Content\Bundle\BundleEntity;

class BundleCollection extends EntityCollection
{
 protected function getExpectedClass(): string
 {
     return BundleEntity::class;
 }
}