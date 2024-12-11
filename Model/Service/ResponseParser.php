<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Service;

class ResponseParser
{
    public function __construct(
        private readonly \Wira\AjaxPrice\Model\Response\ProductDataFactory $productDataFactory,
        private readonly \Wira\AjaxPrice\Model\Response\TierPriceFactory $tierPriceFactory,
    ) {

    }

    /**
     * @return \Wira\AjaxPrice\Model\Response\ProductData[]
     */
    public function parse(string $response): array
    {
        $result = [];
        $unserializedResponse = json_decode($response, true);
        if (isset($unserializedResponse['Product'])) {
            foreach ($unserializedResponse['Product'] as $productData) {
                $productPricesData = $this->productDataFactory->create();
                $productPricesData
                    ->setBeSku($productData['ProdCode'])
                    ->setClassificationCode($productData['ClassCode'])
                    ->setPriceTblCode($productData['PriceTblCode'])
                    ->setPriceMethod($productData['More']['PriceTable']['PriceMethod'] ?? '')
                    ->setQtyMethod($productData['More']['PriceTable']['QtyMethod'] ?? '')
                    ->setQtyAvailable($productData['More']['BranchAvail'][0]['SaleAvail'] ?? null);
                foreach ($productData['More']['PriceTable']['QtyBreak'] ?? [] as $qtyTier) {
                    $tier = $this->tierPriceFactory->create();
                    $tier
                        ->setPrice($qtyTier['UnitPrice'])
                        ->setQty($qtyTier['QtyString'])
                        ->setPriceCode($qtyTier['PriceLvlCode']);
                    $productPricesData->addTier($tier);
                }
                $result[] = $productPricesData;
            }
        }
        return $result;
    }
}
