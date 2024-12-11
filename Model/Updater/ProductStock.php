<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Updater;

class ProductStock implements \Wira\AjaxPrice\Model\UpdaterInterface
{
    public function __construct(
        private readonly \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $getSourceItemsBySku,
        private readonly \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSave,
        private readonly \Magento\Inventory\Model\SourceItemFactory $sourceItemFactory,
    ) {

    }

    public function update(
        \Magento\Catalog\Model\Product $product,
        \Wira\AjaxPrice\Model\Response\ProductData $priceData,
        bool $saveProductData = false
    ): \Magento\Catalog\Model\Product {
        if ($priceData->getQtyAvailable() !== null) {
            if ($priceData->getQtyAvailable() === 0) {
                $product->setData('salable', false);
            }
            $product->setQty($priceData->getQtyAvailable());
            if ($saveProductData) {
                $sourceItem = $this->getSourceItem($product->getSku());
                $sourceItem->setQuantity($priceData->getQtyAvailable());
                $sourceItem->setStatus($priceData->getQtyAvailable() ? 1 : 0);
                $this->sourceItemsSave->execute([$sourceItem]);
            }
        }

        return $product;
    }

    private function getSourceItem(string $sku): \Magento\InventoryApi\Api\Data\SourceItemInterface
    {
        $sourceItem = $this->getSourceItemsBySku->execute($sku);
        if ($sourceItem) {
            return reset($sourceItem);
        }
        $sourceItem = $this->sourceItemFactory->create();
        $sourceItem->setSku($sku);
        $sourceItem->setSourceCode('default');
        return $sourceItem;
    }
}
