<?php

namespace AdeoWeb\Dpd\Model\Location\Source;

use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class All implements OptionSourceInterface
{
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    public function __construct(
        LocationRepositoryInterface $locationRepository
    ) {
        $this->locationRepository = $locationRepository;
    }

    /**
     * {@inheritDoc}
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        $warehouseLocations = $this->locationRepository->getList();

        $options = [
            [
                'label' => '--Select a Location--',
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
}