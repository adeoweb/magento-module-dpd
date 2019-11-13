<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\CollectionRequestManagementInterface;
use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Helper\SubjectReader\CollectionRequestRequest;
use AdeoWeb\Dpd\Helper\Utils;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CollectionRequestImportRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use DateTime;
use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Address;

class CollectionRequestManagement implements CollectionRequestManagementInterface
{
    /**
     * @var CollectionRequestRequest
     */
    private $collectionRequestRequestReader;

    /**
     * @var CollectionRequestImportRequestFactory
     */
    private $collectionRequestImportRequestFactory;

    /**
     * @var ServiceInterface
     */
    private $carrierService;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @var Utils
     */
    private $utils;

    public function __construct(
        CollectionRequestRequest $collectionRequestRequestReader,
        CollectionRequestImportRequestFactory $collectionRequestImportRequestFactory,
        ServiceInterface $carrierService,
        OrderRepositoryInterface $orderRepository,
        LocationRepositoryInterface $locationRepository,
        Utils $utils
    ) {
        $this->collectionRequestRequestReader = $collectionRequestRequestReader;
        $this->collectionRequestImportRequestFactory = $collectionRequestImportRequestFactory;
        $this->carrierService = $carrierService;
        $this->orderRepository = $orderRepository;
        $this->locationRepository = $locationRepository;
        $this->utils = $utils;
    }

    /**
     * @param array $data
     * @return bool
     * @throws LocalizedException
     * @throws Exception
     */
    public function collectionRequest(array $data)
    {
        if (isset($data['request'])) {
            $data = $data['request'];
        }

        $order = $this->loadOrder($data);

        if ($order && $this->collectionRequestRequestReader->readIsSenderUseShippingAddress($data)) {
            $senderAddressEntity = $order->getShippingAddress();
        } else {
            $locationId = $this->collectionRequestRequestReader->readSenderLocation($data);
            $senderAddressEntity = $this->loadLocation($locationId);
        }

        if ($order && $this->collectionRequestRequestReader->readIsRecipientUseShippingAddress($data)) {
            $recipientAddressEntity = $order->getShippingAddress();
        } else {
            $locationId = $this->collectionRequestRequestReader->readRecipientLocation($data);
            $recipientAddressEntity = $this->loadLocation($locationId);
        }

        $senderAddressInfo = $this->getAddressInfo($senderAddressEntity);
        $recipientAddressInfo = $this->getAddressInfo($recipientAddressEntity);

        $pickupNameParts = str_split($senderAddressInfo->getData('name'), 35);
        $recipientNameParts = str_split($recipientAddressInfo->getData('name'), 35);
        $pickupPostCode = $this->utils->formatPostcode($senderAddressInfo->getData('postcode'));
        $recipientPostCode = $this->utils->formatPostcode($recipientAddressInfo->getData('postcode'));

        $collectionRequestImportRequest = $this->collectionRequestImportRequestFactory->create();
        $collectionRequestImportRequest->setPickupName(isset($pickupNameParts[0]) ? $pickupNameParts[0] : null);
        $collectionRequestImportRequest->setPickupName1(isset($pickupNameParts[1]) ? $pickupNameParts[1] : null);
        $collectionRequestImportRequest->setPickupName2(isset($pickupNameParts[2]) ? $pickupNameParts[2] : null);
        $collectionRequestImportRequest->setPickupName3(isset($pickupNameParts[3]) ? $pickupNameParts[3] : null);
        $collectionRequestImportRequest->setPickupStreet($senderAddressInfo->getData('street'));
        $collectionRequestImportRequest->setPickupPostCode($pickupPostCode);
        $collectionRequestImportRequest->setPickupCountry($senderAddressInfo->getData('country'));
        $collectionRequestImportRequest->setPickupCity($senderAddressInfo->getData('city'));
        $collectionRequestImportRequest->setPickupPhone($senderAddressInfo->getData('phone'));
        $collectionRequestImportRequest->setPickupEmail($senderAddressInfo->getData('email'));

        $collectionRequestImportRequest->setRecipientName($recipientNameParts[0] ?? null);
        $collectionRequestImportRequest->setRecipientName2($recipientNameParts[1] ?? null);
        $collectionRequestImportRequest->setRecipientStreet($recipientAddressInfo->getData('street'));
        $collectionRequestImportRequest->setRecipientPostCode($recipientPostCode);
        $collectionRequestImportRequest->setRecipientCountry($recipientAddressInfo->getData('country'));
        $collectionRequestImportRequest->setRecipientCity($recipientAddressInfo->getData('city'));
        $collectionRequestImportRequest->setRecipientPhone($recipientAddressInfo->getData('phone'));
        $collectionRequestImportRequest->setRecipientEmail($recipientAddressInfo->getData('email'));
        $collectionRequestImportRequest->setInfo1($this->mergePackageInfo($data));
        $collectionRequestImportRequest->setInfo2($this->collectionRequestRequestReader->readComment($data));

        $response = $this->carrierService->call($collectionRequestImportRequest);

        if (strpos($response, '201 OK') === false) {
            throw new Exception($response);
        }

        return true;
    }

    /**
     * @param Address|LocationInterface $addressEntity
     * @return DataObject
     */
    private function getAddressInfo($addressEntity)
    {
        $street = $addressEntity instanceof Address ? $addressEntity->getStreet() : $addressEntity->getAddress();
        $country = $addressEntity instanceof Address ? $addressEntity->getCountryId() : $addressEntity->getCountry();
        $phone = $addressEntity instanceof Address ? $addressEntity->getTelephone() : $addressEntity->getPhone();
        $email = $addressEntity instanceof Address ? $addressEntity->getEmail() : '';

        if (is_array($street)) {
            $street = $street[0];
        }

        return new DataObject([
            'name' => $addressEntity->getName(),
            'street' => $street,
            'postcode' => $addressEntity->getPostcode(),
            'country' => $country,
            'city' => $addressEntity->getCity(),
            'phone' => $phone,
            'email' => $email
        ]);
    }

    /**
     * @param array $data
     * @return OrderInterface
     * @throws LocalizedException
     */
    private function loadOrder(array $data)
    {
        $orderId = $this->collectionRequestRequestReader->readOrderId($data);

        return $orderId ? $this->orderRepository->get($orderId) : null;
    }

    /**
     * @param $locationId
     * @return LocationInterface
     * @throws LocalizedException
     */
    private function loadLocation($locationId)
    {
        if (!$locationId) {
            throw new LocalizedException(__('Location is not specified'));
        }

        return $this->locationRepository->getById($locationId);
    }

    /**
     * @param array $data
     * @return string
     * @throws LocalizedException
     */
    private function mergePackageInfo(array $data)
    {
        $numOfParcel = $this->collectionRequestRequestReader->readNumOfParcels($data);
        $totalWeight = $this->collectionRequestRequestReader->readTotalWeight($data);
        $pickupDate = $this->collectionRequestRequestReader->readPickupDate($data);

        $pickupDate = DateTime::createFromFormat('m/d/Y', $pickupDate);

        return '#' . $numOfParcel . 'cl#' . $pickupDate->format('Y-m-d') . '#' . $totalWeight . 'kg';
    }
}
