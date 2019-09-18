<?php

namespace AdeoWeb\Dpd\Api;

use Magento\Sales\Api\Data\ShipmentInterface;

interface CancelParcelsManagementInterface
{
    /**
     * @param ShipmentInterface $shipment
     * @return boolean
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancelParcels(ShipmentInterface $shipment);
}
