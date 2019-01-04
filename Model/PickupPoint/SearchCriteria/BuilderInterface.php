<?php

namespace AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria;

use Magento\Framework\Api\SearchCriteriaInterface;

interface BuilderInterface
{
    /**
     * @param array $filters
     * @return SearchCriteriaInterface
     */
    public function build(array $filters = []);
}