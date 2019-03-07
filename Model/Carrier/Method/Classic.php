<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Model\Carrier\MethodInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Helper\Carrier;

class Classic extends AbstractMethod implements MethodInterface
{
    const DPD_SERVICE = 'D-B2C';

    const CODE = 'classic';

    /**
     * @var string
     */
    protected $code = self::CODE;

    /**
     * @var Config
     */
    private $carrierConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MethodFactory $rateMethodFactory,
        RequestInterface $request,
        Carrier $carrierHelper,
        Config $carrierConfig,
        Restrictions $restrictionsConfig = null,
        array $validators = []
    ) {
        parent::__construct($scopeConfig, $rateMethodFactory, $request, $carrierHelper, $restrictionsConfig, $validators);

        $this->carrierConfig = $carrierConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function processShipmentRequest(CreateShipmentRequest $createShipmentRequest, DataObject $request)
    {
        $createShipmentRequest = parent::processShipmentRequest($createShipmentRequest, $request);

        $deliveryOptions = $this->getDeliveryOptions($request);

        if (!isset($deliveryOptions['delivery_time'])) {
            return $createShipmentRequest;
        }

        $deliveryTimeValue = \explode(' - ', $this->carrierConfig->getCode(
            Config::TYPE_CLASSIC_DELIVERY_TIME,
            $deliveryOptions['delivery_time']
        ));

        $createShipmentRequest->setTimeframeFrom($deliveryTimeValue[0]);
        $createShipmentRequest->setTimeframeTo($deliveryTimeValue[1]);

        return $createShipmentRequest;
    }
}