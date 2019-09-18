<?php

namespace AdeoWeb\Dpd\Api;

use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

interface PrintLabelManagementInterface
{
    /**
     * @param array $labelNumbers
     * @return ResponseInterface|null
     * @throws LocalizedException
     */
    public function printLabels($labelNumbers);
}
