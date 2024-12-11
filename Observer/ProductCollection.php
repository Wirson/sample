<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Observer;

class ProductCollection implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        private readonly \Wira\AjaxPrice\Model\ProductUpdater $productUpdater,
        private readonly \Magento\Customer\Model\Session $customerSession,
    ) {

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getData('collection');
        if ($this->customerSession->isLoggedIn()) {
            $this->productUpdater->updateCollection($collection);
        }
    }
}
