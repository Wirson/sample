<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Service;

class RemotePrice
{
    private const DEFAULT_PAYLOAD = [
        'DateFormatOpt' => 'A',
        'DateDelim' => '-',
        'SavedSchemaID' => '',
        'SavedSchemaCode' => '',
        'Entity' => '1',
        'BranchCode' => '',
        'BranchID' => '',
        'AvailBOM' => false,
        'ChooseOne' => [
            'List' => [],
        ],
        'Product' => [
            [
                'ProdCode' => true,
                'ClassCode' => true,
                'PriceTblCode' => true,
                'More' => [
                    'PriceTable' => [
                        'PriceMethod' => true,
                        'QtyMethod' => true,
                        'QtyBreak' => [
                            [
                                'PriceLvlCode' => true,
                                'PriceLvlDesc' => true,
                                'QtyString' => true,
                                'UnitPrice' => true,
                            ]
                        ]
                    ],
                    'BranchAvail' => [
                        "SaleAvail" => true,
    					'WhseAvailDec' => true,
	    				'SaleAvailString' => true,
                    ]
                ]
            ]
        ]
    ];

    public function __construct(
        private readonly \Wira\ApiIntegration\Model\Api\Client $apiClient,
        private readonly \Wira\AjaxPrice\Model\Service\ResponseParser $responseParser,
    ) {

    }

    /**
     * @return \Wira\AjaxPrice\Model\Response\ProductData[]
     */
    public function get(array $skus): array
    {
        $skusElem = [];
        foreach ($skus as $sku) {
            $skusElem []= ['ProdCode' => $sku];
        }
        $payload = array_merge(self::DEFAULT_PAYLOAD, ['ChooseOne' => ['List' => $skusElem]]);

        return $this->responseParser->parse($this->apiClient->callApi('masterfiles/productV3/export.json', $payload));
    }
}
