<?php declare(strict_types=1);

namespace SwagBundleManager\Core\Checkout\Bundle\Cart;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryHandler\LineItemFactoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class BundleLineItemFactory implements LineItemFactoryInterface
{
    public function supports(string $type): bool
    {
        return $type === BundleCartProcessor::TYPE; // "swagbundle"
    }

    public function create(array $data, SalesChannelContext $context): LineItem
    {
        $id   = $data['id'] ?? Uuid::randomHex();
        $ref  = $data['referencedId'] ?? $data['referenceId'] ?? null;

        $lineItem = new LineItem($id, BundleCartProcessor::TYPE, $ref);

        $lineItem
            ->setStackable(true)                   // set before quantity
            ->setRemovable(true)
            ->setQuantity((int)($data['quantity'] ?? 1))
            ->setLabel($data['label'] ?? 'Bundle');

        return $lineItem;
    }


    public function update(LineItem $lineItem, array $data, SalesChannelContext $context): void
    {
        // TODO: Implement update() method.
    }
}
