<?php declare(strict_types=1);

namespace SwagBundleManager\Storefront\Page\Product\Subscriber;


use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Page\Product\ProductPageCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPageCriteriaSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            ProductPageCriteriaEvent::class => 'onProductPageCriteria',
        ];

    }

    public function onProductPageCriteria(ProductPageCriteriaEvent $event): void
    {
        $event->getCriteria()->addAssociation('bundles');
    }

}