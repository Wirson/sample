<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Controller\Product;

class Price implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    public function __construct(
        private readonly \Wira\AjaxPrice\Model\Html\PriceRenderer $priceRenderer,
        private readonly \Magento\Framework\App\RequestInterface $request,
        private readonly \Magento\Framework\Controller\ResultFactory $resultFactory,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        $sku = $this->request->getParam('sku');
        if ($this->request->isAjax() && $sku) {
            try {
                $resultJson->setData(['result' => $this->priceRenderer->getBySku($sku)]);
            } catch (\Exception $e) {
                $resultJson->setStatusHeader(
                    \Laminas\Http\Response::STATUS_CODE_500,
                    \Laminas\Http\AbstractMessage::VERSION_11,
                    'Server Error.'
                );
                $resultJson->setData(['error' => 'Error while fetching prices.']);
            }
        } else {
            $resultJson->setStatusHeader(
                \Laminas\Http\Response::STATUS_CODE_400,
                \Laminas\Http\AbstractMessage::VERSION_11,
                'Bad Request'
            );
            $resultJson->setData([
                'error' => 'Bad Request 400'
            ]);
        }
        return $resultJson;
    }
}
