<?php ////declare(strict_types=1);
////
////namespace SwagBundleManager\Core\Checkout\Bundle\Cart;
////
////use Doctrine\Common\Collections\Criteria;
////use Shopware\Core\Checkout\Cart\Cart;
////use Shopware\Core\Checkout\Cart\CartBehavior;
////use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
////use Shopware\Core\Checkout\Cart\CartProcessorInterface;
////use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
////use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
////use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
////use Shopware\Core\Checkout\Cart\LineItem\LineItem;
////use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
////use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
////use Shopware\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
////use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
////use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
////use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
////use Shopware\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
////use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
////use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
////use Shopware\Core\System\SalesChannel\SalesChannelContext;
////use SwagBundleManager\Core\Content\Bundle\BundleCollection;
////use SwagBundleManager\Core\Content\Bundle\BundleEntity;
////
////class BundleCartProcessor implements CartProcessorInterface, CartDataCollectorInterface
////{
////
////    public const TYPE = 'swagbundle';
////    public const DISCOUNT_TYPE='sawgbundle-discount';
////    public const DATA_KEY='swag_bundle-';
////    public const DISCOUNT_TYPE_ABSOLUTE='absolute';
////    public const DISCOUNT_TYPE_PERCENTAGE='percentage';
////    private EntityRepository $bundleRepository;
////    private PercentagePriceCalculator $percentagePriceCalculator;
////    private AbsolutePriceCalculator $absolutePriceCalculator;
////    private QuantityPriceCalculator $quantityPriceCalculator;
////
////    public function __construct(
////        EntityRepository $bundleRepository,
////        PercentagePriceCalculator $percentagePriceCalculator,
////        AbsolutePriceCalculator $absolutePriceCalculator,
////        QuantityPriceCalculator $quantityPriceCalculator
////    ) {
////        $this->bundleRepository = $bundleRepository;
////        $this->percentagePriceCalculator = $percentagePriceCalculator;
////        $this->absolutePriceCalculator = $absolutePriceCalculator;
////        $this->quantityPriceCalculator = $quantityPriceCalculator;
////    }
////
////    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
////    {
////        $bundleLineItems = $original->getLineItems()
////            ->filter(self::TYPE);
////
////        if (\count($bundleLineItems) > 0) {
////            return;
////        }
////        $bundles = $this->fetchBundles($bundleLineItems,$data, $context);
////
////
////        /** @var BundleEntity $bundle */
////        foreach ($bundles as $bundle) {
////            $data->set(self::DATA_KEY.$bundle->getId(), $bundle);
////        }
////
////        foreach ($bundleLineItems as $bundleLineItem) {
////            $bundle = $data->get(self::DATA_KEY.$bundleLineItem->getReferencedId());
////
////            $this->enrichBundle($bundleLineItem, $bundle);
////            $this->addMissingProducts($bundleLineItem, $bundle);
////            $this->addDiscount($bundleLineItem, $bundle, $context);
////
////        }
////
////
////    }
////
////    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
////    {
////        $bundleLineItems =$original->getLineItems()
////            ->filterType(self::TYPE);
////        if (\count($bundleLineItems) === 0) {
////            return;
////        }
////        foreach ($bundleLineItems as $bundleLineItem) {
////            $this->caculateChildProductPrices($bundleLineItem, $context);
////            $this->caculatedDiscountPrice($bundleLineItem, $context);
////            $bundleLineItem->setPrice(
////                $bundleLineItem->getChildren()->getPrices()->sum()
////            );
////            $toCalculate->add($bundleLineItem);
////        }
////    }
////
////    private function fetchBundles(LineItemCollection $bundleLineItems, CartDataCollection $data, SalesChannelContext $context)
////    {
////        $bundleIds=$bundleLineItems->getReferenceIds();
////        $filtered=[];
////
////        foreach ($bundleIds as $bundleId) {
////            if ($data->get(self::DATA_KEY.$bundleId)) {
////                continue;
////            }
////            $filtered[]=$bundleId;
////        }
////        if (empty($filtered)) {
////            return new BundleCollection();
////        }
////        $criteria = new Criteria();
////        $criteria->addAssociation('products.deliveryTime');
////        //add annotation here
////
////        $bundles = $this->bundleRepository->search($criteria, $context->getContext())->getEntities();
////        return new $bundles;
////
////
////    }
////
////    private function enrichBundle(LineItem $bundleLineItem, BundleEntity $bundle):void
////    {
////        if(!$bundleLineItem->getLabel()){
////            $bundleLineItem->setLabel($bundle->getTransaltion('name'));
////        }
////
////        $bundleLineItem->setRemovable(true)
////            ->setStackable(true)
////            ->setDeliveryInformation(
////                new DeliveryInformation(
////                    (int)$bundle->getProducts()->first()-getStock(),
////                    (float)$bundle->getProducts()->first()->getWeight(),
////                    $bundle->getProducts()->first()->getShippingFree(),
////                    $bundle->getProducts()->first()->getRestockTime(),
////                    $bundle->getProducts()->first()->getDeliveryTime()?
////                        DeliveryTime::createFromEntity($bundle->getProducts()->first()->getDeliveryTime()):
////                        (new DeliveryTime())->assign([
////                            'name'=>'2 days',
////                            'min'=>1,
////                            'max'=>2,
////                            'unit'=>'day',
////                        ])
////
////                )
////            )
////            ->setQuantity(new QuantityInformation());
////    }
////
////    public function addMissingProducts(LineItem $bundleLineItem, BundleEntity $bundle):void
////    {
////        foreach ($bundle->getProducts()->getIds() as $productId) {
////            if ($bundleLineItem->getChildren()->has($productId)) {
////                continue;
////            }
////            $productLineItem=new LineItem($productId, LineItem::PRODUCT_LINE_ITEM_TYPE,$productId);
////            $bundleLineItem->addChild($productLineItem);
////        }
////    }
////
////    private function addDiscount(LineItem $bundleLineItem, BundleEntity $bundle, SalesChannelContext $context):void
////    {
////        if ($this->getDiscount($bundleLineItem)){
////            return;
////        }
////        $discount = $this->createDiscount($bundle,$context);
////
////        if ($discount){
////            $bundleLineItem->addChild($discount);
////        }
////    }
////    private function getDiscount(LineItem $bundle): ?LineItem
////    {
////        return $bundle->getChildren()->get($bundle->getReferencedId().'-discount');
////    }
////
////    private function createDiscount(BundleEntity $bundleData, SalesChannelContext $context): ?LineItem
////    {
////        if ($bundleData->getDiscount()===0)
////        {
////            return null;
////        }
////        switch ($bundleData->getDiscountType()) {
////            case self::DISCOUNT_TYPE_ABSOLUTE:
////                $priceDefinition = new AbsolutePriceDefinition($bundleData->getDiscount()* -1,$context->getContext()->getCurrencyPrecision());
////                $label ='Absolute bundle discount';
////                break;
////            case self::DISCOUNT_TYPE_PERCENTAGE:
////                $priceDefinition= new PercentagePriceDefinition($bundleData->getDiscount()* -1, $context->getContext()->getCurrencyPrecision());
////                $label =sprintf('Percental bundle voucher (%s%%)',$bundleData->getDiscount());
////                break;
////            default:
////                throw new \RuntimeException('Discount type not supported');
////        }
////        $discount = new LineItem(
////            $bundleData->getId().'-discount',
////            self::DISCOUNT_TYPE,
////            $bundleData->getId()
////        );
////        $discount->setPriceDefinition($priceDefinition)
////        ->setLabel($label)
////        ->setGood(false);
////        return $discount;
////    }
////
////    private function calculateChildProductPrices(LineItem $bundleLineItem, SalesChannelContext $context):void
////    {
////        $products = $bundleLineItem->getChildren()->filter(LineItem::PRODUCT_LINE_ITEM_TYPE);
////
////        foreach ($products as $product) {
////            $priceDefinition = $product->getPriceDefinition();
////            $product-setPrice(
////                $this->quantityPriceCalculator->calcualte($priceDefinition, $context)
////            );
////        }
////    }
////    private function calculateDiscountPrice(LineItem $bundleLineItem, SalesChannelContext $context):void
////    {
////        $discount = $this->getDiscount($bundleLineItem);
////        if (!$discount){
////            return;
////        }
////        $childPrices=$bundleLineItem->getChildren()
////            ->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE)
////            ->getPrices();
////
////        $priceDefinition=$discount->getPriceDefinition();
////        if (!$priceDefinition){
////            return;
////        }
////
////        switch (\get_class($priceDefinition)) {
////            case AbsolutePriceDefinition::class:
////                $price = $this->absolutePriceDefinition->calculate(
////                    $priceDefinition->getPrice(),
////                    $childPrices,
////                    $context,
////                    $bundleLineItem->getQuantity()
////                );
////                break;
////            case PercentagePriceDefinition::class:
////                $price =$this->percentagePriceCalcualtor->calculate(
////                    $priceDefinition->getPercentage(),
////                    $childPrices,
////                    $context,
////                );
////                break;
////            default:
////                throw new \RuntimeException('Invalid discount type');
////        }
////        $discount->setPrice($price);
////
////
////    }
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////
////}
//
//declare(strict_types=1);
//
//namespace SwagBundleManager\Core\Checkout\Bundle\Cart;
//
//use Shopware\Core\Checkout\Cart\Cart;
//use Shopware\Core\Checkout\Cart\CartBehavior;
//use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
//use Shopware\Core\Checkout\Cart\CartProcessorInterface;
//use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
//use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
//use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
//use Shopware\Core\Checkout\Cart\LineItem\LineItem;
//use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
//use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
//use Shopware\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
//use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
//use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
//use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
//use Shopware\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
//use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
//use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
//use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
//use Shopware\Core\System\SalesChannel\SalesChannelContext;
//use SwagBundleManager\Core\Content\Bundle\BundleCollection;
//use SwagBundleManager\Core\Content\Bundle\BundleEntity;
//
//class BundleCartProcessor implements CartProcessorInterface, CartDataCollectorInterface
//{
//    public const TYPE = 'swagbundle';
//    public const DISCOUNT_TYPE = 'swagbundle-discount';
//    public const DATA_KEY = 'swag_bundle-';
//    public const DISCOUNT_TYPE_ABSOLUTE = 'absolute';
//    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';
//
//    private EntityRepository $bundleRepository;
//    private PercentagePriceCalculator $percentagePriceCalculator;
//    private AbsolutePriceCalculator $absolutePriceCalculator;
//    private QuantityPriceCalculator $quantityPriceCalculator;
//
//    public function __construct(
//        EntityRepository $bundleRepository,
//        PercentagePriceCalculator $percentagePriceCalculator,
//        AbsolutePriceCalculator $absolutePriceCalculator,
//        QuantityPriceCalculator $quantityPriceCalculator
//    ) {
//        $this->bundleRepository = $bundleRepository;
//        $this->percentagePriceCalculator = $percentagePriceCalculator;
//        $this->absolutePriceCalculator = $absolutePriceCalculator;
//        $this->quantityPriceCalculator = $quantityPriceCalculator;
//    }
//
//    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
//    {
//        $bundleLineItems = $original->getLineItems()->filterType(self::TYPE);
//
//        if (\count($bundleLineItems) > 0) {
//            return;
//        }
//
//        $bundles = $this->fetchBundles($bundleLineItems, $data, $context);
//
//        /** @var BundleEntity $bundle */
//        foreach ($bundles as $bundle) {
//            $data->set(self::DATA_KEY . $bundle->getId(), $bundle);
//        }
//
//        foreach ($bundleLineItems as $bundleLineItem) {
//            $bundle = $data->get(self::DATA_KEY . $bundleLineItem->getReferencedId());
//
//            $this->enrichBundle($bundleLineItem, $bundle);
//            $this->addMissingProducts($bundleLineItem, $bundle);
//            $this->addDiscount($bundleLineItem, $bundle, $context);
//        }
//    }
//
//    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
//    {
//        foreach ($original->getLineItems()->filterType(self::TYPE) as $bundleLineItem) {
//            // 1. Fetch bundle definition (your own table with bundle->productIds mapping)
//            $bundleId = $bundleLineItem->getReferencedId();
//            $bundle = $this->bundleRepository->search(
//                (new Criteria([$bundleId]))->addAssociation('products'),
//                $context->getContext()
//            )->get($bundleId);
//
//            if (!$bundle) {
//                continue;
//            }
//
//            // 2. Add each product to the bundle
//            foreach ($bundle->getProducts() as $product) {
//                $childItem = new LineItem($product->getId(), LineItem::PRODUCT_LINE_ITEM_TYPE, $product->getId());
//                $childItem->setQuantity(1);
//                $childItem->setStackable(true);
//                $childItem->setRemovable(true);
//
//                // Attach the product data (so prices get calculated correctly)
//                $childItem->setPayload([
//                    'productNumber' => $product->getProductNumber(),
//                ]);
//
//                $bundleLineItem->addChild($childItem);
//            }
//        }}
//
//    private function fetchBundles(LineItemCollection $bundleLineItems, CartDataCollection $data, SalesChannelContext $context): BundleCollection
//    {
//        $bundleIds = $bundleLineItems->getReferenceIds();
//        $filtered = [];
//
//        foreach ($bundleIds as $bundleId) {
//            if ($data->get(self::DATA_KEY . $bundleId)) {
//                continue;
//            }
//            $filtered[] = $bundleId;
//        }
//
//        if (empty($filtered)) {
//            return new BundleCollection();
//        }
//
//        $criteria = new Criteria();
//        $criteria->addAssociation('products.deliveryTime');
//
//        $bundles = $this->bundleRepository->search($criteria, $context->getContext())->getEntities();
//
//        return $bundles;
//    }
//
//    private function enrichBundle(LineItem $bundleLineItem, BundleEntity $bundle): void
//    {
//        if (!$bundleLineItem->getLabel()) {
//            $bundleLineItem->setLabel($bundle->getTranslation('name'));
//        }
//
//        $product = $bundle->getProducts()->first();
//
//        $bundleLineItem->setRemovable(true)
//            ->setStackable(true)
//            ->setDeliveryInformation(
//                new DeliveryInformation(
//                    (int)$product->getStock(),
//                    (float)$product->getWeight(),
//                    $product->getShippingFree(),
//                    $product->getRestockTime(),
//                    $product->getDeliveryTime() ? DeliveryTime::createFromEntity($product->getDeliveryTime()) : (new DeliveryTime())->assign([
//                        'name' => '2 days',
//                        'min' => 1,
//                        'max' => 2,
//                        'unit' => 'day',
//                    ])
//                )
//            )
//            ->setQuantity(new QuantityInformation());
//    }
//
//    public function addMissingProducts(LineItem $bundleLineItem, BundleEntity $bundle): void
//    {
//        foreach ($bundle->getProducts()->getIds() as $productId) {
//            if ($bundleLineItem->getChildren()->has($productId)) {
//                continue;
//            }
//
//            $productLineItem = new LineItem($productId, LineItem::PRODUCT_LINE_ITEM_TYPE, $productId);
//            $bundleLineItem->addChild($productLineItem);
//        }
//    }
//
//    private function addDiscount(LineItem $bundleLineItem, BundleEntity $bundle, SalesChannelContext $context): void
//    {
//        if ($this->getDiscount($bundleLineItem)) {
//            return;
//        }
//
//        $discount = $this->createDiscount($bundle, $context);
//
//        if ($discount) {
//            $bundleLineItem->addChild($discount);
//        }
//    }
//
//    private function getDiscount(LineItem $bundle): ?LineItem
//    {
//        return $bundle->getChildren()->get($bundle->getReferencedId() . '-discount');
//    }
//
//    private function createDiscount(BundleEntity $bundleData, SalesChannelContext $context): ?LineItem
//    {
//        if ($bundleData->getDiscount() === 0) {
//            return null;
//        }
//
//        switch ($bundleData->getDiscountType()) {
//            case self::DISCOUNT_TYPE_ABSOLUTE:
//                $priceDefinition = new AbsolutePriceDefinition($bundleData->getDiscount() * -1, $context->getContext()->getCurrencyPrecision());
//                $label = 'Absolute bundle discount';
//                break;
//            case self::DISCOUNT_TYPE_PERCENTAGE:
//                $priceDefinition = new PercentagePriceDefinition($bundleData->getDiscount() * -1, $context->getContext()->getCurrencyPrecision());
//                $label = sprintf('Percental bundle voucher (%s%%)', $bundleData->getDiscount());
//                break;
//            default:
//                throw new \RuntimeException('Discount type not supported');
//        }
//
//        $discount = new LineItem(
//            $bundleData->getId() . '-discount',
//            self::DISCOUNT_TYPE,
//            $bundleData->getId()
//        );
//
//        $discount->setPriceDefinition($priceDefinition)
//            ->setLabel($label)
//            ->setGood(false);
//
//        return $discount;
//    }
//
//    private function calculateChildProductPrices(LineItem $bundleLineItem, SalesChannelContext $context): void
//    {
//        $products = $bundleLineItem->getChildren()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);
//
//        foreach ($products as $product) {
//            $priceDefinition = $product->getPriceDefinition();
//            $product->setPrice(
//                $this->quantityPriceCalculator->calculate($priceDefinition, $context)
//            );
//        }
//    }
//
//    private function calculateDiscountPrice(LineItem $bundleLineItem, SalesChannelContext $context): void
//    {
//        $discount = $this->getDiscount($bundleLineItem);
//        if (!$discount) {
//            return;
//        }
//
//        $childPrices = $bundleLineItem->getChildren()
//            ->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE)
//            ->getPrices();
//
//        $priceDefinition = $discount->getPriceDefinition();
//        if (!$priceDefinition) {
//            return;
//        }
//
//        switch (\get_class($priceDefinition)) {
//            case AbsolutePriceDefinition::class:
//                $price = $this->absolutePriceCalculator->calculate(
//                    $priceDefinition->getPrice(),
//                    $childPrices,
//                    $context,
//                    $bundleLineItem->getQuantity()
//                );
//                break;
//            case PercentagePriceDefinition::class:
//                $price = $this->percentagePriceCalculator->calculate(
//                    $priceDefinition->getPercentage(),
//                    $childPrices,
//                    $context
//                );
//                break;
//            default:
//                throw new \RuntimeException('Invalid discount type');
//        }
//
//        $discount->setPrice($price);
//    }
//}
//
declare(strict_types=1);

namespace SwagBundleManager\Core\Checkout\Bundle\Cart;

use Doctrine\Common\Collections\Criteria;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryInformation;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryTime;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\LineItem\QuantityInformation;
use Shopware\Core\Checkout\Cart\Price\AbsolutePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria as DALCriteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use SwagBundleManager\Core\Content\Bundle\BundleCollection;
use SwagBundleManager\Core\Content\Bundle\BundleEntity;

class BundleCartProcessor implements CartProcessorInterface, CartDataCollectorInterface
{
    public const TYPE = 'swagbundle';
    public const DISCOUNT_TYPE = 'swagbundle-discount';
    public const DATA_KEY = 'swag_bundle-';
    public const DISCOUNT_TYPE_ABSOLUTE = 'absolute';
    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

    private EntityRepository $bundleRepository;
    private PercentagePriceCalculator $percentagePriceCalculator;
    private AbsolutePriceCalculator $absolutePriceCalculator;
    private QuantityPriceCalculator $quantityPriceCalculator;

    public function __construct(
        EntityRepository          $bundleRepository,
        PercentagePriceCalculator $percentagePriceCalculator,
        AbsolutePriceCalculator   $absolutePriceCalculator,
        QuantityPriceCalculator   $quantityPriceCalculator
    )
    {
        $this->bundleRepository = $bundleRepository;
        $this->percentagePriceCalculator = $percentagePriceCalculator;
        $this->absolutePriceCalculator = $absolutePriceCalculator;
        $this->quantityPriceCalculator = $quantityPriceCalculator;
    }

    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $bundleLineItems = $original->getLineItems()->filterType(self::TYPE);

        if ($bundleLineItems->count() === 0) {
            return;
        }

        $bundles = $this->fetchBundles($bundleLineItems, $data, $context);

        /** @var BundleEntity $bundle */
        foreach ($bundles as $bundle) {
            $data->set(self::DATA_KEY . $bundle->getId(), $bundle);
        }

        foreach ($bundleLineItems as $bundleLineItem) {
            $bundle = $data->get(self::DATA_KEY . $bundleLineItem->getReferencedId());

            if (!$bundle instanceof BundleEntity) {
                continue;
            }

            $this->enrichBundle($bundleLineItem, $bundle);
            $this->addMissingProducts($bundleLineItem, $bundle);
            $this->addDiscount($bundleLineItem, $bundle, $context);
        }
    }

    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $bundleLineItems = $original->getLineItems()->filterType(self::TYPE);

        if ($bundleLineItems->count() === 0) {
            return;
        }

        foreach ($bundleLineItems as $bundleLineItem) {
            $this->calculateChildProductPrices($bundleLineItem, $context);
            $this->calculateDiscountPrice($bundleLineItem, $context);

            $bundleLineItem->setPrice(
                $bundleLineItem->getChildren()->getPrices()->sum()
            );

            $toCalculate->add($bundleLineItem);
        }
    }

    private function fetchBundles(LineItemCollection $bundleLineItems, CartDataCollection $data, SalesChannelContext $context): BundleCollection
    {
        $bundleIds = $bundleLineItems->getReferenceIds();
        $filtered = [];

        foreach ($bundleIds as $bundleId) {
            if ($data->has(self::DATA_KEY . $bundleId)) {
                continue;
            }
            $filtered[] = $bundleId;
        }

        if (empty($filtered)) {
            return new BundleCollection();
        }

        $criteria = new DALCriteria($filtered);
        $criteria->addAssociation('products.deliveryTime');

        $bundles = $this->bundleRepository
            ->search($criteria, $context->getContext())
            ->getEntities();

        return new BundleCollection($bundles->getElements());
    }

    private function enrichBundle(LineItem $bundleLineItem, BundleEntity $bundle): void
    {
        if (!$bundleLineItem->getLabel()) {
            $bundleLineItem->setLabel($bundle->getTranslation('name'));
        }

        $firstProduct = $bundle->getProducts()->first();

        if ($firstProduct) {
            $bundleLineItem->setDeliveryInformation(
                new DeliveryInformation(
                    (int)$firstProduct->getStock(),
                    (float)$firstProduct->getWeight(),
                    $firstProduct->getShippingFree(),
                    $firstProduct->getRestockTime(),
                    $firstProduct->getDeliveryTime()
                        ? DeliveryTime::createFromEntity($firstProduct->getDeliveryTime())
                        : (new DeliveryTime())->assign([
                        'name' => '2 days',
                        'min' => 1,
                        'max' => 2,
                        'unit' => 'day',
                    ])
                )
            );
        }

        $bundleLineItem->setStackable(true)
            ->setRemovable(true)
            ->setQuantityInformation(new QuantityInformation());
    }

    private function addMissingProducts(LineItem $bundleLineItem, BundleEntity $bundle): void
    {
        foreach ($bundle->getProducts()->getIds() as $productId) {
            if ($bundleLineItem->getChildren()->has($productId)) {
                continue;
            }

            $productLineItem = new LineItem(
                $productId,
                LineItem::PRODUCT_LINE_ITEM_TYPE,
                $productId
            );

            $productLineItem
                ->setStackable(true)     // important for quantity syncing
                ->setRemovable(false);   // usually you don’t want to remove bundle parts

            $bundleLineItem->addChild($productLineItem);
        }
    }


    private function addDiscount(LineItem $bundleLineItem, BundleEntity $bundle, SalesChannelContext $context): void
    {
        if ($this->getDiscount($bundleLineItem)) {
            return;
        }

        $discount = $this->createDiscount($bundle, $context);
        if ($discount) {
            $bundleLineItem->addChild($discount);
        }
    }

    private function getDiscount(LineItem $bundle): ?LineItem
    {
        return $bundle->getChildren()->get($bundle->getReferencedId() . '-discount');
    }

    private function createDiscount(BundleEntity $bundleData, SalesChannelContext $context): ?LineItem
    {
        if ($bundleData->getDiscount() === 0) {
            return null;
        }

        // In Shopware 6.5+, CurrencyEntity has no getDecimalPrecision()
        // Safe fallback: 2 (cents), or read from system config
        $precision = $context->getContext()->getRounding()->getDecimals() ?? 2;

        switch ($bundleData->getDiscountType()) {
            case self::DISCOUNT_TYPE_ABSOLUTE:
                $priceDefinition = new AbsolutePriceDefinition(
                    $bundleData->getDiscount() * -1,
                    null, // no filter
                    $precision
                );
                $label = 'Absolute bundle discount';
                break;

            case self::DISCOUNT_TYPE_PERCENTAGE:
                $priceDefinition = new PercentagePriceDefinition(
                    $bundleData->getDiscount() * -1,
                    null, // no filter
                    $precision
                );
                $label = sprintf('Percentage bundle voucher (%s%%)', $bundleData->getDiscount());
                break;

            default:
                throw new \RuntimeException('Discount type not supported');
        }

        $discount = new LineItem(
            $bundleData->getId() . '-discount',
            self::DISCOUNT_TYPE,
            $bundleData->getId()
        );

        $discount->setPriceDefinition($priceDefinition)
            ->setLabel($label)
            ->setGood(false);

        return $discount;
    }

    private function calculateChildProductPrices(LineItem $bundleLineItem, SalesChannelContext $context): void
    {
        $products = $bundleLineItem->getChildren()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);

        foreach ($products as $product) {
            $priceDefinition = $product->getPriceDefinition();

            if ($priceDefinition) {
                $product->setPrice(
                    $this->quantityPriceCalculator->calculate($priceDefinition, $context)
                );
            }
        }
    }

    private function calculateDiscountPrice(LineItem $bundleLineItem, SalesChannelContext $context): void
    {
        $discount = $this->getDiscount($bundleLineItem);
        if (!$discount) {
            return;
        }

        $childPrices = $bundleLineItem->getChildren()
            ->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE)
            ->getPrices();

        $priceDefinition = $discount->getPriceDefinition();
        if (!$priceDefinition) {
            return;
        }

        switch (get_class($priceDefinition)) {
            case AbsolutePriceDefinition::class:
                $price = $this->absolutePriceCalculator->calculate(
                    $priceDefinition->getPrice(),
                    $childPrices,
                    $context,
                    $bundleLineItem->getQuantity()
                );
                break;

            case PercentagePriceDefinition::class:
                $price = $this->percentagePriceCalculator->calculate(
                    $priceDefinition->getPercentage(),
                    $childPrices,
                    $context
                );
                break;

            default:
                throw new \RuntimeException('Invalid discount type');
        }

        $discount->setPrice($price);
    }
}
