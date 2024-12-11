<?php

namespace Wira\AjaxPrice\Model;

interface UpdaterInterface
{
    public function update(
        \Magento\Catalog\Model\Product $product,
        \Wira\AjaxPrice\Model\Response\ProductData $priceData,
        bool $saveProductData = false
    ): \Magento\Catalog\Model\Product;
}
