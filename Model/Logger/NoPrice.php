<?php

declare(strict_types=1);

namespace Wira\AjaxPrice\Model\Logger;

class NoPrice
{
    private ?\Zend_Log $logger = null;

    public function logEntry(string $sku, string $description): void
    {
        if (!$this->logger) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/NoPriceTableError-' . date('m-d-H:i:s') . '.log');
            $writer->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $this->logger = $logger;
        }
        $this->logger->info("$sku - $description");
    }
}
