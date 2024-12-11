<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Observer;

class CheckoutRefreshQuote implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        private readonly \Magento\Checkout\Model\Session $checkoutSession,
    ) {

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->checkoutSession->getQuoteId()) {
            $this->checkoutSession->getQuote()->collectTotals();
        }
    }
}
