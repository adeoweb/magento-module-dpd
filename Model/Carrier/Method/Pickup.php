<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Helper\Utils;
use AdeoWeb\Dpd\Model\Carrier\MethodInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Helper\Carrier;

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
            $restrictionsConfig,
            $validators
        );

        $this->pickupPointRepository = $pickupPointRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function processShipmentRequest(CreateShipmentRequest $createShipmentRequest, DataObject $request)
    {
        $createShipmentRequest = parent::processShipmentRequest($createShipmentRequest, $request);

        $deliveryOptions = $this->getDeliveryOptions($request);

        if (!isset($deliveryOptions['pickup_point_id'])) {
            return $createShipmentRequest;
        }

        $pickupPoint = $this->pickupPointRepository->getById($deliveryOptions['pickup_point_id']);

        $createShipmentRequest->setParcelshopId($pickupPoint->getApiId());
        $createShipmentRequest->setFetchAllByCountryFlag(true);

        return $createShipmentRequest;
    }

    /**
     * @param DataObject $deliveryOptions
     * @return bool
     * @throws LocalizedException
     */
    public function validateDeliveryOptions(DataObject $deliveryOptions)
    {
        $pickupPointId = $deliveryOptions->getPickupPointId();

        if (!$pickupPointId) {
            throw new LocalizedException(__('Please select DPD pickup point.'));
        }

        return parent::validateDeliveryOptions($deliveryOptions);
    }
}
