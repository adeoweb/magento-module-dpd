<?php

namespace AdeoWeb\Dpd\Api\Data;

interface LocationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Public method
     *
     * @return \AdeoWeb\Dpd\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * Public method
     *
     * @param \AdeoWeb\Dpd\Api\Data\LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
