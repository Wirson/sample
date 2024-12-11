<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Cron;

class UpdateProduct
{
    public function __construct(
        private readonly \Wira\AjaxPrice\Model\ProductUpdater $productUpdater,
        private readonly \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        private readonly \Psr\Log\LoggerInterface $logger,
    ) {

    }

    public function execute()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter('be_sku', ['notnull' => true]);
        try {
            $this->productUpdater->updateCollection($collection, true);
            $this->logger->info('Price Update Cronjob success');
        } catch (\Exception $e) {
            $this->logger->error('Price Update Cronjob failure:', ['exception' => $e->getMessage()]);
        }
    }
}
