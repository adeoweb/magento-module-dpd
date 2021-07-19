<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\Adminhtml\File;

use Magento\Framework\Message\ManagerInterface;

interface CreatePdfProcessorInterface
{
    /**
     * @param array $parcels
     * @param ManagerInterface $messageManager
     */
    public function process(array $parcels, ManagerInterface $messageManager): void;
}
