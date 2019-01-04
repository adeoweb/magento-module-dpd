<?php

namespace AdeoWeb\Dpd\Model\Service;

use AdeoWeb\Dpd\Config\Api;
use AdeoWeb\Dpd\Model\Service\Dpd\ResponseFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleListInterface;
use Psr\Log\LoggerInterface;
use Zend_Http_Client_Adapter_Curl;

class Dpd implements ServiceInterface
{
    const PARAM_AUTH_USERNAME = 'username';
    const PARAM_AUTH_PASSWORD = 'password';

    /**
     * @var Api
     */
    private $apiConfig;

    /**
     * @var Dpd\ResponseFactory
     */
    private $responseFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        Api $apiConfig,
        ResponseFactory $responseFactory,
        LoggerInterface $logger,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata
    ) {
        $this->apiConfig = $apiConfig;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param RequestInterface $request
     * @return null|ResponseInterface|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function call(RequestInterface $request)
    {
        if (!$request) {
            return null;
        }

        $config = array(
            'adapter' => Zend_Http_Client_Adapter_Curl::class,
            'curloptions' => array(CURLOPT_SSL_VERIFYPEER => false),
        );

        $requestParams = $this->getRequestParams($request);

        $client = new \Zend_Http_Client($this->getEndpointUrl($request), $config);
        $client->setParameterPost($requestParams->toArray());

        try {
            $rawResponse = $client->request($request->getMethod());
            $rawResponse = $rawResponse->getBody();
        } catch (\Zend_Http_Exception $e) {
            $this->logger->info('HTTP Request ERROR: ' . $e->getMessage());

            throw new LocalizedException(
                __('Something went wrong while doing a request to DPD service. Please contact system administrator for more information.')
            );
        }

        if ($this->apiConfig->isDebugMode()) {
            $this->logger->info('REQUEST: [Endpoint: ' . $request->getEndpoint() . '] [Parameters: ' . $requestParams->toJson());
            $this->logger->info('RESPONSE: ' . $rawResponse);
        }

        if ($request->isFile()) {
            return $rawResponse;
        }

        $rawResponse = \json_decode($rawResponse, true);

        return $this->responseFactory->create($rawResponse);
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getEndpointUrl(RequestInterface $request)
    {
        $apiUrl = $this->apiConfig->getUrl();

        if (!$apiUrl) {
            throw new \RuntimeException('DPD Module is not configured. API Url missing');
        }

        if (\substr($apiUrl, -1) !== '/') {
            $apiUrl .= '/';
        }

        return $apiUrl . 'ws-mapper-rest/' . $request->getEndpoint();
    }

    /**
     * @param RequestInterface $request
     * @return DataObject
     * @throws LocalizedException
     */
    private function getRequestParams(RequestInterface $request)
    {
        $authParams = $this->getAuthParams();
        $clientInfoParams = $this->getClientInfoParams();

        return new DataObject(
            array_merge($authParams, $clientInfoParams, $request->getParams())
        );
    }

    /**
     * @return array
     */
    private function getAuthParams()
    {
        $username = $this->apiConfig->getUsername();
        $password = $this->apiConfig->getPassword();

        if (!$username || !$password) {
            throw new \RuntimeException('DPD Module is not configured. API Username or/and password missing');
        }

        return [
            self::PARAM_AUTH_USERNAME => $username,
            self::PARAM_AUTH_PASSWORD => $password
        ];
    }

    /**
     * @return array
     */
    private function getClientInfoParams()
    {
        $module = $this->moduleList->getOne('AdeoWeb_Dpd');

        return [
            'PluginVersion' => isset($module['setup_version']) ? $module['setup_version'] : '',
            'EshopVersion' => 'Magento ' .  $this->productMetadata->getVersion()
        ];
    }
}