<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Updater;

class CustomerProductPrice implements \Wira\AjaxPrice\Model\UpdaterInterface
{
    public function __construct(
        private readonly \Wira\ExtendCustomer\Model\CustomerDataProvider $customerDataProvider,
    ) {

    }

    public function update(
        \Magento\Catalog\Model\Product $product,
        \Wira\AjaxPrice\Model\Response\ProductData $priceData,
        bool $saveProductData = false
    ): \Magento\Catalog\Model\Product {
        $priceLvlCode = $this->customerDataProvider->getPriceLvlByCode($priceData->getClassificationCode());
        if ($priceLvlCode) {
            foreach ($priceData->getTiers() as $priceTier) {
                if ($priceTier->getPriceCode() == $priceLvlCode) {
                    if ($priceData->getPriceMethod() === 'C') {
                        $price = $priceTier->getPrice() / 100;
                    } else {
                        $price = $priceTier->getPrice();
                    }
                    $product->setPrice($price);
                    break;
                }
            }
        }
        return $product;
    }
}
