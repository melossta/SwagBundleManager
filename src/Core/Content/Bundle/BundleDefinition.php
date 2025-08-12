<?php declare(strict_types=1);

namespace SwagBundleManager\Core\Content\Bundle;

use BundleExampleStep2\Core\Content\Bundle\Aggregate\BundleProduct\BundleProductDefinition;
use BundleExampleStep2\Core\Content\Bundle\Aggregate\BundleTranslation\BundleTranslationDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class BundleDefinition extends EntityDefinition
{

    public function getEntityName(): string
    {
        return "swag_bundle";
    }
    public function getEntityClass(): string
    {
        return BundleEntity::class;
    }
    public function getCollectionClass(): string
    {
        return BundleCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
           (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new Required()),
//            new TranslatedField('name'),
            (new StringField('discount_type', 'discountType'))->addFlags(new Required()),
            (new FloatField('discount','discount'))->addFlags(new Required()),

            new TranslationsAssociationField(BundleTranslationDefinition::class, 'swag_bundle'),
            //new ManyToManyAssociationField('products', ProductDefinition::class, 'product_id', BundleProductDefinition::class, 'bundle_id'),
            //new ManyToManyAssociationField('products', ProductDefinition::class, BundleProductDefinition::class, 'bundle_id', 'product_id'),
            new ManyToManyAssociationField(
                'products',
                ProductDefinition::class,
                BundleProductDefinition::class,
                'bundle_id',
                'product_id'
            )


            ]);
    }
}
