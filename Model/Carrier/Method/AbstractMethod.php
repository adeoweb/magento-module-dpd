<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\Carrier\Method;

use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Helper\Utils;
use AdeoWeb\Dpd\Model\Carrier\ValidatorInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use Exception;
use InvalidArgumentException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Sales\Model\Order;
use Magento\Shipping\Helper\Carrier;
use Magento\Store\Model\ScopeInterface;
use Zend_Measure_Weight;
use Magento\Framework\App\ProductMetadataInterface;
use AdeoWeb\Dpd\Model\Provider\MetaData\ModuleMetaDataInterface;

use function sprintf;
use function is_numeric;
use function count;

abstract class AbstractMethod
{
    const DPD_SERVICE = '';

    const XML_PATH_CARRIER_TITLE = 'carriers/dpd/title';

    const XML_PATH_METHOD_FREESHIPPING = 'carriers/dpd/%s/free_shipping_enable';
    const XML_PATH_METHOD_FREESHIPPING_ORDER_VALUE = 'carriers/dpd/%s/free_shipping_subtotal';
    const XML_PATH_METHOD_PRICE = 'carriers/dpd/%s/price';
    const XML_PATH_METHOD_MAX_WEIGHT = 'carriers/dpd/%s/max_weight';
    const XML_PATH_METHOD_TITLE = 'carriers/dpd/%s/name';

    const FIELD_DEST_COUNTRY_ID = 'dest_country_id';
    const FIELD_PACKAGE_WEIGHT = 'package_weight';

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
     * @var Restrictions
     */
    protected $restrictionsConfig;

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

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var ModuleMetaDataInterface
     */
    private $moduleMetaData;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MethodFactory $rateMethodFactory,
        RequestInterface $request,
        Carrier $carrierHelper,
        Serializer $serializer,
        Utils $utils,
        ProductMetadataInterface $productMetadata,
        ModuleMetaDataInterface $moduleMetaData,
        Restrictions $restrictionsConfig = null,
        array $validators = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->validators = $validators;
        $this->httpRequest = $request;
        $this->carrierHelper = $carrierHelper;
        $this->restrictionsConfig = $restrictionsConfig;
        $this->serializer = $serializer;
        $this->productMetadata = $productMetadata;
        $this->moduleMetaData = $moduleMetaData;
        $this->utils = $utils;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getLabel(): string
    {
        $methodTitle = $this->scopeConfig->getValue(
            sprintf(self::XML_PATH_METHOD_TITLE, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );

        if (empty($methodTitle)) {
            return '';
        }

        return (string)$methodTitle;
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getCarrierTitle(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CARRIER_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return Method
     * @throws Exception
     */
    public function getRateResult(): Method
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
    public function validate(): bool
    {
        foreach ($this->validators as $validator) {
            if (!$validator instanceof ValidatorInterface) {
                throw new InvalidArgumentException(
                    'Validator must be instance of ' . ValidatorInterface::class
                );
            }

            if (!$validator->validate(['request' => $this->request, 'method_code' => $this->code])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param CreateShipmentRequest $createShipmentRequest
     * @param DataObject $request
     * @return CreateShipmentRequest
     * @throws Exception
     */
    public function processShipmentRequest(
        CreateShipmentRequest $createShipmentRequest,
        DataObject $request
    ): CreateShipmentRequest {
        /** @var Order $order */
        $order = $request->getOrderShipment()->getOrder();

        $packageCount = count($request->getData('packages'));
        $parcelType = $this->getParcelType($request);
        $postcode = $this->utils->formatPostcode($request->getData('recipient_address_postal_code'));

        $createShipmentRequest->setName1($request->getData('recipient_contact_person_name'));
        $createShipmentRequest->setName2($request->getData('recipient_contact_company_name'));
        $createShipmentRequest->setStreet($request->getData('recipient_address_street'));
        $createShipmentRequest->setCity($request->getData('recipient_address_city'));
        $createShipmentRequest->setPostcode($postcode);
        $createShipmentRequest->setCountry($request->getData('recipient_address_country_code'));
        $createShipmentRequest->setPhone($request->getData('recipient_contact_phone_number'));
        $createShipmentRequest->setEmail($request->getData('recipient_email'));
        $createShipmentRequest->setNumOfParcel($packageCount);
        $createShipmentRequest->setParcelType($parcelType);
        $createShipmentRequest->setWeight($this->getWeight($request, $packageCount));
        $createShipmentRequest->setOrderNumber($order->getIncrementId());
        $createShipmentRequest->setIdmSmsNumber($request->getData('recipient_contact_phone_number'));
        $createShipmentRequest->setOrderNumber3($this->getMagentoModuleVersions());

        if ($this->isCod($request)) {
            $createShipmentRequest->setCodAmount($order->getGrandTotal());
        }

        return $createShipmentRequest;
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    public function getFreeShipping(): bool
    {
        return (bool)$this->scopeConfig->isSetFlag(
            sprintf(self::XML_PATH_METHOD_FREESHIPPING, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return float
     * @codeCoverageIgnore
     */
    public function getFreeShippingOrderValue(): float
    {
        return (float)$this->scopeConfig->getValue(
            sprintf(self::XML_PATH_METHOD_FREESHIPPING_ORDER_VALUE, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param RateRequest $request
     * @codeCoverageIgnore
     */
    public function setRequest(RateRequest $request): void
    {
        $this->request = $request;
    }

    /**
     * @return float
     * @throws Exception
     */
    protected function getPackageValue(): float
    {
        return $this->request->getPackageValue();
    }

    /**
     * @return float
     * @throws Exception
     */
    protected function getPackageValueWithDiscount(): float
    {
        return $this->request->getPackageValueWithDiscount();
    }

    /**
     * @return float
     * @throws Exception
     */
    protected function getPrice(): float
    {
        if ($this->isFreeShipping()) {
            return 0.00;
        }

        if ($restrictedPrice = $this->getRestrictedPrice()) {
            return (float)$restrictedPrice;
        }

        return (float)$this->scopeConfig->getValue(
            sprintf(self::XML_PATH_METHOD_PRICE, $this->getCode()),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     * @throws Exception
     */
    protected function getRestrictedPrice(): ?string
    {
        if (!$this->restrictionsConfig) {
            return null;
        }

        return $this->restrictionsConfig->getByCountryWeight(
            $this->request->getData(self::FIELD_DEST_COUNTRY_ID),
            $this->request->getData(self::FIELD_PACKAGE_WEIGHT)
        );
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function isFreeShipping(): bool
    {
        if (!$this->getFreeShipping()) {
            return false;
        }

        $freeShippingValue = $this->getFreeShippingOrderValue();

        return $this->getPackageValueWithDiscount() >= $freeShippingValue;
    }

    /**
     * @return float
     * @codeCoverageIgnore
     */
    protected function getMaxWeight(): float
    {
        return (float)$this->scopeConfig->getValue(sprintf(self::XML_PATH_METHOD_MAX_WEIGHT, $this->getCode()));
    }

    /**
     * @param DataObject $request
     * @param int $splitInto
     * @return float
     */
    protected function getWeight(DataObject $request, int $splitInto = 1): float
    {
        $totalWeightKilogram = 0.00;

        foreach ($request->getPackages() as $package) {
            $params = $package['params'];

            $totalWeightKilogram += $this->carrierHelper->convertMeasureWeight(
                $params['weight'],
                $params['weight_units'],
                Zend_Measure_Weight::KILOGRAM
            );
        }
        return (float)sprintf('%.3f', $totalWeightKilogram / $splitInto);
    }

    /**
     * @param $request
     * @return string
     */
    protected function getParcelType($request): string
    {
        $result = $this->getServiceType();

        if ($this->isCod($request)) {
            $result .= '-COD';
        }

        if ($this->httpRequest->getParam('dpd_include_return_labels') == '1') {
            $result .= '-RETURN';
        }

        if ($this->httpRequest->getParam('dpd_document_return_service') == '1') {
            $result .= '-DOCRET';
        }

        return $result;
    }

    /**
     * @param $request
     * @return bool
     */
    protected function isCod(DataObject $request): bool
    {
        /** @var Order $order */
        $order = $request->getOrderShipment()->getOrder();

        return $order->getPayment()->getMethod() === 'cashondelivery';
    }

    /**
     * @return string
     */
    protected function getServiceType(): string
    {
        return static::DPD_SERVICE;
    }

    /**
     * @param DataObject $deliveryOptions
     * @return bool
     */
    public function validateDeliveryOptions(DataObject $deliveryOptions): bool
    {
        return true;
    }

    /**
     * @param DataObject $request
     * @return array
     * @throws LocalizedException
     */
    protected function getDeliveryOptions(DataObject $request): array
    {
        /** @var Order $order */
        $order = $request->getOrderShipment()->getOrder();
        $dpdDeliveryOptions = $order->getData('dpd_delivery_options');

        if (empty($dpdDeliveryOptions)) {
            return [];
        }


        return $this->serializer->unserialize($order->getData('dpd_delivery_options'));
    }

    private function getMagentoModuleVersions(): string
    {
        return sprintf(
            "MG%s|%s", $this->productMetadata->getVersion(), $this->moduleMetaData->getVersion()
        );
    }
}
