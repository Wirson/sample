<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Updater;

class WebProductPrice implements \Wira\AjaxPrice\Model\UpdaterInterface
{
    public function __construct(
        private readonly \Wira\AjaxPrice\Model\Config $config,
        private readonly \Wira\AjaxPrice\Model\Logger\NoPrice $noPriceLogger,
        private readonly \Magento\Catalog\Model\ResourceModel\Product $productResource,
        private readonly \Magento\Catalog\Api\Data\TierPriceInterfaceFactory $tierPriceFactory,
        private readonly \Magento\Catalog\Api\TierPriceStorageInterface $tierPriceStorage,
    ) {

    }

    public function update(
        \Magento\Catalog\Model\Product $product,
        \Wira\AjaxPrice\Model\Response\ProductData $priceData,
        bool $saveProductData = false
    ): \Magento\Catalog\Model\Product {

        if ($saveProductData && !$priceData->getPriceTblCode()) {
            $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
            $this->productResource->saveAttribute($product, 'status');
            $product->setPrice(0);
            $this->productResource->saveAttribute($product, 'price');
            $this->noPriceLogger->logEntry($product->getData('be_sku'), $product->getName());

            return $product;
        }

        if ($priceData->getTiers()) {
            $tierPrices = $this->tierPriceStorage->get([$product->getSku()]);
            if ($tierPrices) {
                $this->tierPriceStorage->delete($tierPrices);
            }
        }

        $tierObjects = [];
        foreach ($priceData->getTiers() as $priceTier) {
            if ((string) $priceTier->getPriceCode() === $this->config->getWebPriceLvlCode()) {
                if ($priceTier->getQty() > 1) {
                    $price = $this->getPrice($priceData, $priceTier);

                    $guestTier = $this->tierPriceFactory->create();
                    $guestTier->setSku($product->getSku());
                    $guestTier->setQuantity($priceTier->getQty());
                    $guestTier->setPrice($price);
                    $guestTier->setPriceType(\Magento\Catalog\Api\Data\TierPriceInterface::PRICE_TYPE_FIXED);
                    $guestTier->setCustomerGroup('all groups');
                    $guestTier->setWebsiteId('0');
                    $tierObjects [] = $guestTier;
                } else {
                    if (!(float)$product->getPrice() && $product->getStatus() != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        $this->productResource->saveAttribute($product, 'status');
                    }

                    $product->setPrice($this->getPrice($priceData, $priceTier));
                    $this->productResource->saveAttribute($product, 'price');
                }
            }
        }
        if ($tierObjects) {
            $this->tierPriceStorage->replace($tierObjects);
        }

        return $product;
    }

    private function getPrice(
        \Wira\AjaxPrice\Model\Response\ProductData $priceData,
        \Wira\AjaxPrice\Model\Response\TierPrice $priceTier
    ): float {
        if ($priceData->getPriceMethod() === 'C') {
            $price = $priceTier->getPrice() / 100;
        } else {
            $price = $priceTier->getPrice();
        }

        return (float) $price;
    }
}
