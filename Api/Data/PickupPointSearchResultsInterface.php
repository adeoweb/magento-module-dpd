<?php

namespace AdeoWeb\Dpd\Api\Data;

interface PickupPointSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Public method
     *
     * @return \AdeoWeb\Dpd\Api\Data\PickupPointInterface[]
     */
    public function getItems();

    /**
     * Public method
     *
     * @param \AdeoWeb\Dpd\Api\Data\PickupPointInterface[] $items
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function setItems(array $items);
}
