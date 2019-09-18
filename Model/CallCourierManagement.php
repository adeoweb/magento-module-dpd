<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\CallCourierManagementInterface;
use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Helper\SubjectReader\CallCourierRequest;
use AdeoWeb\Dpd\Model\Config\CallCourierOrderCount;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupOrderSaveRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use Magento\Framework\Exception\LocalizedException;

class CallCourierManagement implements CallCourierManagementInterface
{
    /**
     * @var PickupOrderSaveRequestFactory
     */
    private $pickupOrderSaveRequestFactory;

    /**
     * @var ServiceInterface
     */
    private $carrierService;

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @var CallCourierRequest
     */
    private $callCourierRequestReader;

    /**
     * @var Config\CallCourierOrderCount
     */
    private $callCourierOrderCountConfig;

    public function __construct(
        PickupOrderSaveRequestFactory $pickupOrderSaveRequestFactory,
        ServiceInterface $carrierService,
        LocationRepositoryInterface $locationRepository,
        CallCourierRequest $callCourierRequestReader,
        CallCourierOrderCount $callCourierOrderCountConfig
    ) {
        $this->pickupOrderSaveRequestFactory = $pickupOrderSaveRequestFactory;
        $this->carrierService = $carrierService;
        $this->locationRepository = $locationRepository;
        $this->callCourierRequestReader = $callCourierRequestReader;
        $this->callCourierOrderCountConfig = $callCourierOrderCountConfig;
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function callCourier(array $data)
    {
        if (isset($data['request'])) {
            $data = $data['request'];
        }

        $warehouseId = $this->callCourierRequestReader->readWarehouseId($data);
        $warehouse = $this->getWarehouseById($warehouseId);

        $pickupTime = $this->formatDate(
            $this->callCourierRequestReader->readPickupDate($data),
            $this->callCourierRequestReader->readPickupTime($data)
        );

        $senderWorkUntil = $this->formatDate(
            $this->callCourierRequestReader->readPickupDate($data),
            $this->callCourierRequestReader->readWorkUntil($data)
        );

        $orderNr = $this->callCourierOrderCountConfig->register();

        $pickupOrderSaveRequest = $this->pickupOrderSaveRequestFactory->create();
        $pickupOrderSaveRequest->setOrderNr($orderNr);
        $pickupOrderSaveRequest->setSenderName($warehouse->getName());
        $pickupOrderSaveRequest->setSenderAddress($warehouse->getAddress());
        $pickupOrderSaveRequest->setSenderCity($warehouse->getCity());
        $pickupOrderSaveRequest->setSenderCountry($warehouse->getCountry());
        $pickupOrderSaveRequest->setSenderPostalCode($warehouse->getPostcode());
        $pickupOrderSaveRequest->setSenderAddAddress($warehouse->getAdditionalInfo());
        $pickupOrderSaveRequest->setSenderContact($warehouse->getContactName());
        $pickupOrderSaveRequest->setSenderPhone($warehouse->getPhone());
        $pickupOrderSaveRequest->setSenderWorkUntil($senderWorkUntil);
        $pickupOrderSaveRequest->setPickupTime($pickupTime);
        $pickupOrderSaveRequest->setWeight($this->callCourierRequestReader->readTotalWeight($data));
        $pickupOrderSaveRequest->setParcelsCount($this->callCourierRequestReader->readNumOfParcels($data));
        $pickupOrderSaveRequest->setComment($this->callCourierRequestReader->readComment($data));

        $response = $this->carrierService->call($pickupOrderSaveRequest);

        if (\strpos($response, 'DONE') === false) {
            throw new \Exception($response);
        }

        return true;
    }

    /**
     * @param string|int $warehouseId
     * @return LocationInterface
     * @throws LocalizedException
     */
    private function getWarehouseById($warehouseId)
    {
        return $this->locationRepository->getById($warehouseId);
    }

    /**
     * @param string $date
     * @param string $time
     * @return string
     * @throws \Exception
     */
    private function formatDate($date, $time)
    {
        if (\strpos($time, ':') === false) {
            throw new LocalizedException(__('Invalid pickup time value'));
        }

        $timeTokens = \explode(':', $time);

        $pickupTime = \DateTime::createFromFormat('m/d/Y', $date);
        $pickupTime->setTime($timeTokens[0], $timeTokens[1]);

        if ($pickupTime < (new \Datetime())) {
            throw new LocalizedException(__('Incorrect pickup time'));
        }

        return $pickupTime->format('Y-m-d H:i:s');
    }
}
