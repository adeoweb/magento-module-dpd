<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\Shipping\Block\Adminhtml\Order\Packaging;

use Magento\Shipping\Block\Adminhtml\Order\Packaging\Grid as BaseGrid;

class Grid extends BaseGrid
{
    protected function _construct(): void
    {
        $this->setTemplate('AdeoWeb_Dpd::order/packaging/grid.phtml');
    }
}
