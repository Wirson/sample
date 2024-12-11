<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model;

class ProductUpdater
{
    /**
     * @param \Wira\AjaxPrice\Model\UpdaterInterface[] $updaterPool
     */
    public function __construct(
        private readonly \Wira\AjaxPrice\Model\Service\RemotePrice $remotePrice,
        private readonly \Psr\Log\LoggerInterface $logger,
        private readonly array $updaterPool = [],
    ) {

    }

    public function updateSingle(\Magento\Catalog\Model\Product $product): \Magento\Catalog\Model\Product
    {
        if ($product->getData('be_sku')) {
            $this->update($product, $this->remotePrice->get([$product->getData('be_sku')]));
        }

        return $product;
    }

    public function updateCollection(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection,
        bool $saveProductData = false
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection {
        $beSkus = $collection->getColumnValues('be_sku');
        $this->logger->info(count($beSkus) . ' SKUs loaded to update: ' . implode(',', $beSkus));
        if (!array_contains($beSkus, null)) {
            $pricesData = $this->remotePrice->get($beSkus);
            foreach ($collection as $product) {
                $this->update($product, $pricesData, $saveProductData);
            }
        }

        return $collection;
    }

    private function update(
        \Magento\Catalog\Model\Product $product,
        array $pricesData,
        bool $saveProductData = false
    ): \Magento\Catalog\Model\Product {
        /** @var \Wira\AjaxPrice\Model\Response\ProductData $priceData */
        foreach ($pricesData as $priceData) {
            if ($priceData->getBeSku() === $product->getData('be_sku')) {
                foreach ($this->updaterPool as $updater) {
                    $product = $updater->update($product, $priceData, $saveProductData);
                }
            }
        }

        return $product;
    }
}
