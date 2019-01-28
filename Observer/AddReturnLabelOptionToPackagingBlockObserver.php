<?php

namespace AdeoWeb\Dpd\Observer;

use AdeoWeb\Dpd\Block\Adminhtml\Order\Packaging\ReturnLabelField;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Shipping\Block\Adminhtml\Order\Packaging;

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

        if (!$block instanceof Packaging) {
            return;
        }

        $transport = $observer->getEvent()->getData('transport');
        $html = $transport->getHtml();

        $additional = $block->getLayout()->createBlock(ReturnLabelField::class)->toHtml();

        $html = \str_replace('<div id="packaging_window">', '<div id="packaging_window">' . $additional, $html);

        $transport->setHtml($html);
    }
}