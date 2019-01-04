<?php

namespace AdeoWeb\Dpd\Model\Location\Source;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class Warehouse implements OptionSourceInterface
{
    /**
     * @var \AdeoWeb\Dpd\Api\LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    public function __construct(
        \AdeoWeb\Dpd\Api\LocationRepositoryInterface $locationRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    )
    {
        $this->locationRepository = $locationRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        $warehouseLocations = $this->locationRepository->getList($this->getWarehouseLocationSearchCriteria());

        $options = [
            [
                'label' => __('--Select a Warehouse--'),
                'value' => ''
            ]
        ];

        foreach ($warehouseLocations->getItems() as $location) {
            $options[] = [
                'label' => $location->getName(),
                'value' => $location->getLocationId(),
            ];
        }
        return $options;
    }

    /**
     * @return SearchCriteria
     */
    private function getWarehouseLocationSearchCriteria()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter('type', LocationInterface::TYPE_WAREHOUSE);

        return $searchCriteriaBuilder->create();
    }
}