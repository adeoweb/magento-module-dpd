<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\CancelParcelsManagementInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelDeleteRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;

class CancelParcelsManagement implements CancelParcelsManagementInterface
{
    /**
     * @var ParcelDeleteRequestFactory
     */
    private $parcelDeleteRequestFactory;

    /**
     * @var ServiceInterface
     */
    private $carrierService;

    /**
     * @var ShipmentTrackRepositoryInterface
     */
    private $trackRepository;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    public function __construct(
        ParcelDeleteRequestFactory $parcelDeleteRequestFactory,
        ServiceInterface $carrierService,
        ShipmentTrackRepositoryInterface $trackRepository,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->parcelDeleteRequestFactory = $parcelDeleteRequestFactory;
        $this->carrierService = $carrierService;
        $this->trackRepository = $trackRepository;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param ShipmentInterface $shipment
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancelParcels(ShipmentInterface $shipment)
    {
        $tracks = $shipment->getTracks();
        $parcels = [];

        foreach ($tracks as $track) {
            $parcels[] = $track->getTrackNumber();
        }

        $parcelDeleteRequest = $this->parcelDeleteRequestFactory->create();
        $parcelDeleteRequest->setParcels($parcels);

        $response = $this->carrierService->call($parcelDeleteRequest);

        if ($response->hasError()) {
            throw new \Exception($response->getErrorMessage());
        }

        foreach ($tracks as $track) {
            $this->trackRepository->delete($track);
        }

        $shipment->setShippingLabel(null);
        $this->shipmentRepository->save($shipment);

        return true;
    }
}
