<?php declare(strict_types=1);

namespace SwagBundleManager\Core\Content\Bundle;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class BundleDefinition extends EntityDefinition
{

    public function getEntityName(): string
    {
        return "swag_bundle";
    }

    protected function defineFields(): FieldCollection
    {
        // TODO: Implement defineFields() method.
    }
}
