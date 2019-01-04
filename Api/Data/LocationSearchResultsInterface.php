<?php

namespace AdeoWeb\Dpd\Api\Data;

interface LocationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \AdeoWeb\Dpd\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * @param \AdeoWeb\Dpd\Api\Data\LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
