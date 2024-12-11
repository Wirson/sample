<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Plugin;

class Product
{
    public function __construct(
        private readonly \Wira\AjaxPrice\Model\ProductUpdater $productUpdater,
        private readonly \Magento\Customer\Model\Session $customerSession,
    ) {

    }

    public function afterAfterLoad(
        \Magento\Catalog\Model\Product $subject,
        \Magento\Catalog\Model\Product $result
    ): \Magento\Catalog\Model\Product {
        if ($this->customerSession->isLoggedIn()) {
            $result = $this->productUpdater->updateSingle($result);
        }

        return $result;
    }
}
