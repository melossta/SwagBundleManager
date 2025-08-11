<?php declare(strict_types=1);

namespace BundleManager\Core\Content\Bundle;


use BundleExampleStep2\Core\Content\Bundle\Aggregate\BundleTranslation\BundleTranslationCollection;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class BundleEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $discountType;
    /**
     * @var float
     */
    protected $discount;

    /**
     * @var BundleTranslationCollection
     */
    protected $translations;

    /**
     * @var ProductCollection|null
     */
    protected $products;

    /**
     * @return string
     */
    public function getName() :string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDiscountType(): string
    {
        return $this->discountType;
    }

    /**
     * @param string $discountType
     */
    public function setDiscountType(string $discountType): void
    {
        $this->discountType = $discountType;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getTranslations(): BundleTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(BundleTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getProducts(): ?ProductCollection
    {
        return $this->products;
    }

    public function setProducts(?ProductCollection $products): void
    {
        $this->products = $products;
    }
}
