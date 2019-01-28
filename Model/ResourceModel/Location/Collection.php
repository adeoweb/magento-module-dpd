<?php

namespace AdeoWeb\Dpd\Model\ResourceModel\Location;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @codeCoverageIgnore
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \AdeoWeb\Dpd\Model\Location::class,
            \AdeoWeb\Dpd\Model\ResourceModel\Location::class
        );
    }
}
