<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Api\Data\ShipmentInterface;
use AdeoWeb\Dpd\Model\Email\SendTrackingDataEmail;

class SendTrackingDataObserver implements ObserverInterface
{
    /**
     * @var SendTrackingDataEmail
     */
    private $trackingDataEmail;

    /**
     * @param SendTrackingDataEmail $trackingDataEmail
     */
    public function __construct(
        SendTrackingDataEmail $trackingDataEmail
    ) {
        $this->trackingDataEmail = $trackingDataEmail;
    }

    /**
     * Public method
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $track = $observer->getEvent()->getTrack();
        $shipment = $track->getShipment();

        if (!$shipment instanceof ShipmentInterface) {
            return;
        }

        $order = $shipment->getOrder();

        $carrierCode = $order->getShippingMethod(true)->getCarrierCode();

        if (!$carrierCode === 'dpd') {
            return;
        }

        $this->trackingDataEmail->send($order, $shipment);
    }
}
