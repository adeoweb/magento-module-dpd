<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Helper\Utils;
use AdeoWeb\Dpd\Model\Carrier\MethodInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use AdeoWeb\Dpd\Model\Shipping\DeliveryOptions;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Helper\Carrier;
use Magento\Framework\App\ProductMetadataInterface;
use AdeoWeb\Dpd\Model\Provider\MetaData\ModuleMetaDataInterface;

class Pickup extends AbstractMethod implements MethodInterface
{
    const DPD_SERVICE = 'PS';
    const CODE = 'pickup';

    protected $code = self::CODE;

    /**
     * @var PickupPointRepositoryInterface
     */
    private $pickupPointRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MethodFactory $rateMethodFactory,
        RequestInterface $request,
        Carrier $carrierHelper,
        Serializer $serializer,
        PickupPointRepositoryInterface $pickupPointRepository,
        Utils $utils,
        ProductMetadataInterface $productMetadata,
        ModuleMetaDataInterface $moduleMetaData,
        Restrictions $restrictionsConfig = null,
        array $validators = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateMethodFactory,
            $request,
            $carrierHelper,
            $serializer,
            $utils,
            $productMetadata,
            $moduleMetaData,
            $restrictionsConfig,
            $validators
        );

        $this->pickupPointRepository = $pickupPointRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function processShipmentRequest(CreateShipmentRequest $createShipmentRequest, DataObject $request): CreateShipmentRequest
    {
        $deliveryOptions = $this->getDeliveryOptions($request);

        if (!isset($deliveryOptions[DeliveryOptions::INDEX_API_ID])) {
            return parent::processShipmentRequest($createShipmentRequest, $request);
        }

        $pickupPoint = $this->pickupPointRepository->getByApiId($deliveryOptions[DeliveryOptions::INDEX_API_ID]);

        $request->setData('recipient_address_street', $pickupPoint->getStreet());
        $request->setData('recipient_address_city', $pickupPoint->getCity());
        $request->setData('recipient_address_postal_code', $pickupPoint->getPostcode());
        $request->setData('recipient_address_country_code', $pickupPoint->getCountry());

        $createShipmentRequest = parent::processShipmentRequest($createShipmentRequest, $request);
        $createShipmentRequest->setParcelshopId($pickupPoint->getApiId());
        $createShipmentRequest->setFetchAllByCountryFlag(true);

        return $createShipmentRequest;
    }

    /**
     * @param DataObject|DeliveryOptions $deliveryOptions
     * @return bool
     * @throws LocalizedException
     */
    public function validateDeliveryOptions(DataObject $deliveryOptions): bool
    {
        $apiId = $deliveryOptions->getApiId();

        if (!$apiId) {
            throw new LocalizedException(__('Please select DPD pickup point.'));
        }

        return parent::validateDeliveryOptions($deliveryOptions);
    }
}
