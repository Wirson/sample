<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model;

class Config implements \Wira\ApiIntegration\Api\EndpointConfigInterface
{
    private const XML_PATH_DEBUG_MODE = 'api_integration/remote_price/debug';
    private const XML_PATH_CACHE_ENABLED = 'api_integration/remote_price/cache';
    private const XML_PATH_WEB_PRICE_LVL_CODE = 'api_integration/remote_price/web_price_lvl_code';

    public function __construct(
        private readonly \Magento\Framework\App\Config\ScopeConfigInterface $config,
    ) {

    }

    public function isDebugEnabled(int $storeId = null): bool
    {
        return $this->config->isSetFlag(
            self::XML_PATH_DEBUG_MODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }

    public function isCacheEnabled(int $storeId = null): bool
    {
        return $this->config->isSetFlag(
            self::XML_PATH_CACHE_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }

    public function getWebPriceLvlCode(int $storeId = null): string
    {
        return (string) $this->config->getValue(
            self::XML_PATH_WEB_PRICE_LVL_CODE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }
}
