<?php

namespace AdeoWeb\Dpd\Model;

use AdeoWeb\Dpd\Helper\Config;
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
    const CODE = 'dpd';
    const URL_TRACKING = 'https://tracking.dpd.de/status/en_US/parcel/%s';

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
     * @var Dpd\Request\ParcelPrintRequestFactory
     */
    private $parcelPrintRequestFactory;

    /**
     * @var ParcelStatusRequestFactory
     */
    private $parcelStatusRequestFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
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
        Dpd\Request\ParcelPrintRequestFactory $parcelPrintRequest,
        ParcelStatusRequestFactory $parcelStatusRequestFactory,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $xmlSecurity, $xmlElFactory, $rateFactory,
            $rateMethodFactory, $trackFactory, $trackErrorFactory, $trackStatusFactory, $regionFactory, $countryFactory,
            $currencyFactory, $directoryData, $stockRegistry, $data);

        $this->carrierConfig = $carrierConfig;
        $this->methodFactoryPool = $methodFactoryPool;
        $this->createShipmentRequestFactory = $createShipmentRequestFactory;
        $this->dpdService = $dpdService;
        $this->parcelPrintRequestFactory = $parcelPrintRequest;
        $this->parcelStatusRequestFactory = $parcelStatusRequestFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates() || !$this->getConfigFlag('active')) {
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
        return \explode(',', $this->getConfigData('allowed_methods'));
    }

    /**
     * {@inheritDoc}
     */
    public function processAdditionalValidation(DataObject $request)
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

        $tracking->setUrl(sprintf(self::URL_TRACKING, $trackingNumber));

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

        if ($request->getStoreId() != null) {
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
            throw new \Exception($response->getErrorMessage());
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
        $parcelPrintRequest = $this->parcelPrintRequestFactory->create();
        $parcelPrintRequest->setParcels($parcelData);
        $parcelPrintRequest->setPrintFormat('A4');

        $response = $this->dpdService->call($parcelPrintRequest);

        return $response;
    }
}