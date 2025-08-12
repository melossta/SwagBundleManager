<?php //declare(strict_types=1);
//
//namespace SwagBundleManager\Products\Core\Content\Product;
//
//use Shopware\Core\Content\Product\ProductDefinition;
//use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
//use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
//use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
//use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
//use SwagBundleManager\Core\Content\Bundle\BundleDefinition;
//
//class ProductExtension extends EntityExtension
//{
//    public function getDefinitionClass():string
//    {
//        return ProductDefinition::class;
//    }
//    public function getEntityName(): string
//    {
//        // TODO: Implement getEntityName() method.
//    }
//    public function extendFields(FieldCollection $collection): void
//    {
//        $collection->add(
//            (new ManyToOneAssociationField(
//             'bundles',
//                BundleDefinition::class,
//                BundleDefinition::class,
//                'product_id',
//                'bundle_id'
//            ))->addFlags(new Inherited())
//        );
//    }
//}
declare(strict_types=1);

namespace SwagBundleManager\Products\Core\Content\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use SwagBundleManager\Core\Content\Bundle\Aggregate\BundleProduct\BundleProductDefinition;
use SwagBundleManager\Core\Content\Bundle\BundleDefinition;


class ProductExtension extends EntityExtension
{

    public function getEntityName(): string
    {
        return ProductDefinition::ENTITY_NAME;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new ManyToManyAssociationField(
                'bundles',
                BundleDefinition::class,
                BundleProductDefinition::class,
                'product_id',
                'bundle_id'
            )
        );
    }

}
