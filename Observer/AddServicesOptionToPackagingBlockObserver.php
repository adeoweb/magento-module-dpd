<?php

namespace AdeoWeb\Dpd\Observer;

use AdeoWeb\Dpd\Block\Adminhtml\Order\Packaging\Services;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Shipping\Block\Adminhtml\Order\Packaging;

use function str_replace;

class AddServicesOptionToPackagingBlockObserver implements ObserverInterface
{
    /**
     * Add packaging block
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getData('block');

        if (!$block instanceof Packaging) {
            return;
        }

        $transport = $observer->getEvent()->getData('transport');
        $html = $transport->getHtml();

        $additional = $block->getLayout()->createBlock(Services::class)->toHtml();

        $html = str_replace('<div id="packaging_window">', '<div id="packaging_window">' . $additional, $html);

        $transport->setHtml($html);
    }
}
