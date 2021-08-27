<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Helper\Utils;
use AdeoWeb\Dpd\Config\Api;
use AdeoWeb\Dpd\Model\Carrier\MethodFactoryPool;
use AdeoWeb\Dpd\Model\Service\Dpd;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelStatusRequestFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory as RateErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Simplexml\ElementFactory;
use Magento\Shipping\Model\Tracking\Result\ErrorFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Magento\Shipping\Model\Tracking\ResultFactory as TrackResultFactory;
use Psr\Log\LoggerInterface;

class Carrier extends AbstractCarrierOnline implements CarrierInterface
{
    public const CODE = 'dpd';
    private const DEFAULT_LANG_CODE = 'lt';
    private const URL_TRACKING_LINKS = [
        'ee' => 'https://www.dpdgroup.com/ee/mydpd/tmp/basicsearch?lang=et&parcel_id=%s',
        'lv' => 'https://www.dpdgroup.com/lv/mydpd/tmp/basicsearch?lang=lv&parcel_id=%s',
        'lt' => 'https://www.dpdgroup.com/lt/mydpd/tmp/basicsearch?lang=lt&parcel_id=%s'
    ];

    /**
     * {@inheritDoc}
     */
    protected $_code = self::CODE;

    /**
     * {@inheritDoc}
     */
    protected $_isFixed = true;

    /**
     * @var Config
     */
    private $carrierConfig;

    /**
     * @var Carrier\MethodFactoryPool
     */
    private $methodFactoryPool;

    /**
     * @var Dpd\Request\CreateShipmentRequestFactory
     */
    private $createShipmentRequestFactory;

    /**
     * @var Dpd
     */
    private $dpdService;

    /**
     * @var ParcelStatusRequestFactory
     */
    private $parcelStatusRequestFactory;

    /**
     * @var PrintLabelManagementInterface
     */
    private $printLabelManagement;

    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var Api
     */
    private $apiConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Utils $utils,
        Api $apiConfig,
        RateErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        Security $xmlSecurity,
        ElementFactory $xmlElFactory,
        ResultFactory $rateFactory,
        MethodFactory $rateMethodFactory,
        TrackResultFactory $trackFactory,
        ErrorFactory $trackErrorFactory,
        StatusFactory $trackStatusFactory,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        CurrencyFactory $currencyFactory,
        Data $directoryData,
        StockRegistryInterface $stockRegistry,
        Config $carrierConfig,
        MethodFactoryPool $methodFactoryPool,
        Dpd $dpdService,
        Dpd\Request\CreateShipmentRequestFactory $createShipmentRequestFactory,
        ParcelStatusRequestFactory $parcelStatusRequestFactory,
        PrintLabelManagementInterface $printLabelManagement,
        array $data = []
    ) {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );

        $this->carrierConfig = $carrierConfig;
        $this->methodFactoryPool = $methodFactoryPool;
        $this->createShipmentRequestFactory = $createShipmentRequestFactory;
        $this->dpdService = $dpdService;
        $this->parcelStatusRequestFactory = $parcelStatusRequestFactory;
        $this->printLabelManagement = $printLabelManagement;
        $this->utils = $utils;
        $this->apiConfig = $apiConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()) {
            return false;
        }

        $result = $this->_rateFactory->create();

        foreach ($this->getAllowedMethods() as $methodCode) {
            $method = $this->methodFactoryPool->getInstance($methodCode, $request);

            if (!$method || !$method->validate()) {
                continue;
            }

            $result->append($method->getRateResult());
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedMethods()
    {
        $methods = \explode(',', $this->getConfigData('allowed_methods'));

        return \array_combine($methods, $methods);
    }

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function processAdditionalValidation(DataObject $request)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * Used for compatibility between Magento versions
     */
    public function proccessAdditionalValidation(DataObject $request)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getTrackingInfo($trackingNumber)
    {
        $parcelStatusRequest = $this->parcelStatusRequestFactory->create();
        $parcelStatusRequest->setParcelNumber($trackingNumber);

        $response = $this->dpdService->call($parcelStatusRequest);

        $tracking = $this->_trackStatusFactory->create();
        $tracking->setCarrier(self::CODE);
        $tracking->setCarrierTitle($this->getConfigData('title'));
        $tracking->setTracking($trackingNumber);

        if (!$response->hasError()) {
            $tracking->setTrackSummary($response->getBody('parcel_status'));
        }

        $apiLanguageCode = $this->getApiLanguageCode();

        $tracking->setUrl(sprintf(self::URL_TRACKING_LINKS[$apiLanguageCode], $trackingNumber));

        return $tracking;
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function requestToShipment($request)
    {
        $packages = $request->getPackages();

        if (!is_array($packages) || !$packages) {
            throw new LocalizedException(__('No packages for request'));
        }

        if ($request->getStoreId() !== null) {
            $this->setStore($request->getStoreId());
        }

        $parcels = $this->_doShipmentRequest($request);

        $data = [];
        $response = new DataObject();

        try {
            foreach ($parcels as $parcelData) {
                $result = $this->doLabelRequest($parcelData);

                $data[] = [
                    'tracking_number' => $parcelData,
                    'label_content' => $result,
                ];

                if (!isset($isFirstRequest)) {
                    $request->setMasterTrackingId($parcelData);
                    $isFirstRequest = false;
                }
            }

            $response->setData('info', $data);
        } catch (\Exception $e) {
            $response->setData('errors', $e->getMessage());
        }

        return $response;
    }

    /**
     * @param DataObject $request
     * @return array
     * @throws \Exception
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        $requestShippingMethod = $request->getData('shipping_method');
        $method = $this->methodFactoryPool->getInstance($requestShippingMethod, null);

        if (!$method) {
            throw new LocalizedException(__('DPD Carrier method "%1" does not exist', $requestShippingMethod));
        }

        $createShipmentRequest = $this->createShipmentRequestFactory->create();
        $createShipmentRequest = $method->processShipmentRequest($createShipmentRequest, $request);

        $response = $this->dpdService->call($createShipmentRequest);

        if ($response->hasError()) {
            throw new LocalizedException(__('API Error: ' . $response->getErrorMessage()));
        }

        return $response->getBody('pl_number');
    }

    /**
     * @param $parcelData
     * @return Service\ResponseInterface|string|null
     * @throws LocalizedException
     */
    protected function doLabelRequest($parcelData)
    {
        return $this->printLabelManagement->printLabels([$parcelData]);
    }

    private function getApiLanguageCode(): string
    {
        $apiUrl = $this->apiConfig->getUrl();

        if (empty($apiUrl)) {
            return self::DEFAULT_LANG_CODE;
        }

        return $this->utils->getTldFromUrl($apiUrl);
    }
}
