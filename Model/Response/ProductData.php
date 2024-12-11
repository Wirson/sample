<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Response;

/**
 * @method string getBeSku()
 * @method self setBeSku(string $beSku)
 * @method string getPriceMethod()
 * @method self setPriceMethod(string $priceMethod)
 * @method string getQtyMethod()
 * @method self setQtyMethod(string $qtyMethod)
 * @method int|null getQtyAvailable()
 * @method self setQtyAvailable(?int $qtyAvailable)
 * @method string getClassificationCode()
 * @method self setClassificationCode(string $classificationCode)
 * @method string|null getPriceTblCode()
 * @method self setPriceTblCode(string|null $priceTblCode)
 * @method \Wira\AjaxPrice\Model\Response\TierPrice[] getTiers()
 * @method self setTiers(array $tiers)
 */
class ProductData extends \Magento\Framework\DataObject
{
    public function __construct(array $data = ['tiers' => []])
    {
        parent::__construct($data);
    }

    public function addTier(\Wira\AjaxPrice\Model\Response\TierPrice $tier): self
    {
        $tiers = $this->getTiers();
        $tiers[] = $tier;
        return $this->setTiers($tiers);
    }
}
