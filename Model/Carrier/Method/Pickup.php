<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Model\Carrier\MethodInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
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
        PickupPointRepositoryInterface $pickupPointRepository,
        Restrictions $restrictionsConfig = null,
        array $validators = []
    ) {
        parent::__construct($scopeConfig, $rateMethodFactory, $request, $carrierHelper, $restrictionsConfig, $validators);

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
}