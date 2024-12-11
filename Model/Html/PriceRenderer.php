<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Html;

class PriceRenderer
{
    public function __construct(
        \Magento\Framework\App\ViewInterface $view, //need to inject, otherwise layout is not loaded
        private readonly \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        private readonly \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly \Magento\Framework\App\Cache\StateInterface $cacheState,
        private readonly \Magento\Framework\Registry $registry,
        private readonly \Magento\Framework\View\LayoutInterface $layout,
    ) {

    }

    public function getBySku(string $sku): string
    {
        $product = $this->productRepository->get($sku);
        $this->disableBlockHtmlCache();
        $this->registerProduct($product);
        $this->prepareLayout([
            'default',
            'catalog_product_view',
            'catalog_product_view_type_simple',
            'product_view_price',
        ]);

        return $this->layout->renderElement('product.info.price', false);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByIds(array $ids, ?string $displayMode): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $ids, 'in')->create();
        $productList = $this->productRepository->getList($searchCriteria);
        $result = [];

        $this->disableBlockHtmlCache();
        $this->prepareLayout([
            'default',
            'catalog_product_view',
            'catalog_product_view_type_simple',
            'product_view_price',
        ]);
        /** @var \Magento\Catalog\Block\Product\ListProduct $priceBlock */
        $priceBlock = $this->layout->createBlock(\Magento\Catalog\Block\Product\ListProduct::class);
        foreach ($productList->getItems() as $product) {
            $this->registerProduct($product);
            if ($displayMode === 'table') {
                $result[$product->getId()] = $priceBlock->getProductPriceHtml(
                    $product,
                    \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                    'table_view'
                );
            } else {
                $result[$product->getId()] = $priceBlock->getProductPrice($product);
            }
        }

        return $result;
    }

    protected function disableBlockHtmlCache(): void
    {
        $this->cacheState->setEnabled(\Magento\Framework\View\Element\AbstractBlock::CACHE_GROUP, false);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareLayout(array $handles): void
    {
        $this->layout->getUpdate()->load($handles);
        $this->layout->generateXml();
    }

    protected function registerProduct(\Magento\Catalog\Model\Product $product): void
    {
        $this->registry->register('product', $product, true);
        $this->registry->register('current_product', $product, true);
    }
}
