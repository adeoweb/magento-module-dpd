<?php

namespace AdeoWeb\Dpd\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddReturnLabelOptionToPackagingBlockObserver implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');

        if (!$block instanceof \Magento\Shipping\Block\Adminhtml\Order\Packaging) {
            return;
        }

        $transport = $observer->getEvent()->getData('transport');
        $html = $transport->getHtml();

        $additional = $block->getLayout()->createBlock(\AdeoWeb\Dpd\Block\Adminhtml\Order\Packaging\ReturnLabelField::class)->toHtml();

        $html = \str_replace('<div id="packaging_window">', '<div id="packaging_window">' . $additional, $html);

        $transport->setHtml($html);
    }
}