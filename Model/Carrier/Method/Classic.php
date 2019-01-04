<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Config\Classic\Restrictions;
use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Model\Carrier\MethodInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Helper\Carrier;
use Magento\Store\Model\ScopeInterface;

class Classic extends AbstractMethod implements MethodInterface
{
    const DPD_SERVICE = 'D-B2C';

    const CODE = 'classic';

    /**
     * @var string
     */
    protected $code = self::CODE;

    /**
     * @var Restrictions
     */
    private $restrictionsConfig;

    /**
     * @var Config
     */
    private $carrierConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MethodFactory $rateMethodFactory,
        Restrictions $restrictionsConfig,
        RequestInterface $request,
        Carrier $carrierHelper,
        Config $carrierConfig,
        array $validators = []
    ) {
        parent::__construct($scopeConfig, $rateMethodFactory, $request, $carrierHelper, $validators);

        $this->restrictionsConfig = $restrictionsConfig;
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

    /**
     * {@inheritDoc}
     */
    public function getPrice()
    {
        if ($this->isFreeShipping()) {
            return 0.00;
        }

        $restrictions = $this->restrictionsConfig->getByCountry($this->request->getData('dest_country_id'));

        if (!isset($restrictions['price']) || empty($restrictions['price'])) {
            return parent::getPrice();
        }

        return $restrictions['price'];
    }
}