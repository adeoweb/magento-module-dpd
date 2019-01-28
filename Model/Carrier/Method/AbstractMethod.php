<?php

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Model\Carrier\ValidatorInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use http\Exception\RuntimeException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Sales\Model\Order;
use Magento\Shipping\Helper\Carrier;
use Magento\Store\Model\ScopeInterface;

abstract class AbstractMethod
{
    const DPD_SERVICE = '';

    const XML_PATH_CARRIER_TITLE = 'carriers/dpd/title';

    const XML_PATH_METHOD_FREESHIPPING = 'carriers/dpd/%s/free_shipping_enable';
    const XML_PATH_METHOD_FREESHIPPING_ORDER_VALUE = 'carriers/dpd/%s/free_shipping_subtotal';
    const XML_PATH_METHOD_PRICE = 'carriers/dpd/%s/price';
    const XML_PATH_METHOD_MAX_WEIGHT = 'carriers/dpd/%s/max_weight';
    const XML_PATH_METHOD_TITLE = 'carriers/dpd/%s/name';

    /**
     * @var string
     */
    protected $code = '';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MethodFactory
     */
    protected $rateMethodFactory;

    /**
     * @var RateRequest
     */
    protected $request;

    /**
     * @var array
     */
    private $validators;

    /**
     * @var Carrier
     */
    private $carrierHelper;

    /**
     * @var RequestInterface
     */
    private $httpRequest;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MethodFactory $rateMethodFactory,
        RequestInterface $request,
        Carrier $carrierHelper,
        array $validators = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->validators = $validators;
        $this->httpRequest = $request;
        $this->carrierHelper = $carrierHelper;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getLabel()
    {
        $methodTitle = $this->scopeConfig->getValue(
            \sprintf(self::XML_PATH_METHOD_TITLE, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );

        if (empty($methodTitle)) {
            return '';
        }

        return $methodTitle;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getCarrierTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CARRIER_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     * @throws \Exception
     */
    public function getRateResult()
    {
        $rateResultMethod = $this->rateMethodFactory->create();

        if (!$this->validate()) {
            return $rateResultMethod;
        }

        $rateResultMethod->setData('carrier', \AdeoWeb\Dpd\Model\Carrier::CODE);
        $rateResultMethod->setData('carrier_title', $this->getCarrierTitle());
        $rateResultMethod->setData('method_title', $this->getLabel());
        $rateResultMethod->setData('method', $this->getCode());
        $rateResultMethod->setPrice($this->getPrice());
        $rateResultMethod->setData('cost', $this->getPrice());

        return $rateResultMethod;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        foreach ($this->validators as $validator) {
            if (!$validator instanceof ValidatorInterface) {
                throw new \InvalidArgumentException(
                    'Validator must be instance of ' . ValidatorInterface::class
                );
            }

            if (!$validator->validate([
                'request' => $this->request,
                'method_code' => $this->code
            ])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param CreateShipmentRequest $createShipmentRequest
     * @param DataObject $request
     * @return CreateShipmentRequest
     * @throws \Exception
     */
    public function processShipmentRequest(CreateShipmentRequest $createShipmentRequest, DataObject $request)
    {
        /** @var Order $order */
        $order = $request->getOrderShipment()->getOrder();

        $packageCount = \count($request->getData('packages'));
        $parcelType = $this->getParcelType($request);

        $createShipmentRequest->setName1($request->getData('recipient_contact_person_name'));
        $createShipmentRequest->setName2($request->getData('recipient_contact_company_name'));
        $createShipmentRequest->setStreet($request->getData('recipient_address_street'));
        $createShipmentRequest->setCity($request->getData('recipient_address_city'));
        $createShipmentRequest->setPostcode($request->getData('recipient_address_postal_code'));
        $createShipmentRequest->setCountry($request->getData('recipient_address_country_code'));
        $createShipmentRequest->setPhone($request->getData('recipient_contact_phone_number'));
        $createShipmentRequest->setEmail($request->getData('recipient_email'));
        $createShipmentRequest->setNumOfParcel($packageCount);
        $createShipmentRequest->setParcelType($parcelType);
        $createShipmentRequest->setWeight($this->getWeight($request, $packageCount));
        $createShipmentRequest->setOrderNumber($order->getIncrementId());
        $createShipmentRequest->setIdmSmsNumber($request->getData('recipient_contact_phone_number'));

        if ($this->isCod($request)) {
            $createShipmentRequest->setCodAmount($order->getGrandTotal());
        }

        return $createShipmentRequest;
    }

    /**
     * @return float
     * @codeCoverageIgnore
     */
    public function getFreeShipping()
    {
        return $this->scopeConfig->isSetFlag(
            sprintf(self::XML_PATH_METHOD_FREESHIPPING, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return float
     * @codeCoverageIgnore
     */
    public function getFreeShippingOrderValue()
    {
        return $this->scopeConfig->getValue(
            sprintf(self::XML_PATH_METHOD_FREESHIPPING_ORDER_VALUE, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param RateRequest $request
     * @codeCoverageIgnore
     */
    public function setRequest(RateRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return float
     * @throws \Exception
     */
    protected function getPackageValue()
    {
        return $this->request->getPackageValue();
    }


    /**
     * @return float
     * @throws \Exception
     */
    protected function getPrice()
    {
        if ($this->isFreeShipping()) {
            return 0.00;
        }

        return (float)$this->scopeConfig->getValue(
            sprintf(self::XML_PATH_METHOD_PRICE, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function isFreeShipping()
    {
        if (!$this->getFreeShipping()) {
            return false;
        }

        $freeShippingValue = $this->getFreeShippingOrderValue();

        return (\is_numeric($freeShippingValue) && $this->getPackageValue() >= $freeShippingValue);
    }

    /**
     * @return mixed
     * @codeCoverageIgnore
     */
    protected function getMaxWeight()
    {
        return $this->scopeConfig->getValue(sprintf(self::XML_PATH_METHOD_MAX_WEIGHT, $this->getCode()));
    }

    /**
     * @param DataObject $request
     * @param int $splitInto
     * @return float
     */
    protected function getWeight(DataObject $request, $splitInto = 1)
    {
        $totalWeightKilogram = 0.00;

        foreach ($request->getPackages() as $package) {
            $params = $package['params'];

            $totalWeightKilogram += $this->carrierHelper->convertMeasureWeight(
                $params['weight'],
                $params['weight_units'],
                \Zend_Measure_Weight::KILOGRAM
            );
        }
        return sprintf('%.3f', $totalWeightKilogram / $splitInto);
    }

    /**
     * @param $request
     * @return string
     */
    protected function getParcelType($request)
    {
        $result = static::DPD_SERVICE;

        if ($this->isCod($request)) {
            $result .= '-COD';
        }

        if ($this->httpRequest->getParam('dpd_include_return_labels') == '1') {
            $result .= '-RETURN';
        }

        return $result;
    }

    /**
     * @param $request
     * @return bool
     */
    protected function isCod(DataObject $request)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $request->getOrderShipment()->getOrder();

        return $order->getPayment()->getMethod() === 'cashondelivery';
    }

    /**
     * @param DataObject $request
     * @return array
     */
    protected function getDeliveryOptions(DataObject $request)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $request->getOrderShipment()->getOrder();

        return Serializer::unserialize($order->getData('dpd_delivery_options'));
    }
}