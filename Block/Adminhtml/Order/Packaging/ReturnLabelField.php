<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Order\Packaging;

class ReturnLabelField extends \Magento\Framework\View\Element\Template
{
    const TEMPLATE = 'order/packaging/return_label_field.phtml';

    public function _construct()
    {
        $this->setTemplate(self::TEMPLATE);
    }
}