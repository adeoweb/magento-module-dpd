<?php

namespace AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\Builder;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Model\PickupPoint\SearchCriteria\BuilderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

class PickupPoint extends AbstractBuilder implements BuilderInterface
{
    /**
     * @param array $filters
     * @return SearchCriteriaInterface
     */
    public function build(array $filters = [])
    {
        $searchCriteriaBuilder = $this->getInstance();

        $searchCriteriaBuilder = $this->addSortField($searchCriteriaBuilder, PickupPointInterface::CITY);

        foreach ($filters as $field => $value) {
            if ($value === null) {
                continue;
            }

            $searchCriteriaBuilder = $this->addFilter($searchCriteriaBuilder, $field, $value);
        }

        return $searchCriteriaBuilder->create();
    }
}