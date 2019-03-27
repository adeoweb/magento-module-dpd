<?php

namespace AdeoWeb\Dpd\Model\Service;

use AdeoWeb\Dpd\Config\Api;
use AdeoWeb\Dpd\Model\Service\Dpd\ResponseFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Module\ModuleListInterface;
use Psr\Log\LoggerInterface;

class Dpd implements ServiceInterface
{
    const PARAM_AUTH_USERNAME = 'username';
    const PARAM_AUTH_PASSWORD = 'password';
    const API_METHOD_PATH = 'ws-mapper-rest/';

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

    /**
     * @var ZendClientFactory
     */
    private $httpClientFactory;

    public function __construct(
        Api $apiConfig,
        ResponseFactory $responseFactory,
        LoggerInterface $logger,
        ModuleListInterface $moduleList,
        ProductMetadataInterface $productMetadata,
        ZendClientFactory $httpClientFactory
    ) {
        $this->apiConfig = $apiConfig;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->moduleList = $moduleList;
        $this->productMetadata = $productMetadata;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * @param RequestInterface $request
     * @return null|ResponseInterface|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function call(RequestInterface $request)
    {
        $config = [
            'verifypeer' => false,
            'timeout' => 300
        ];

        $requestParams = $this->getRequestParams($request);

        try {
            $client = $this->httpClientFactory->create([
                'uri' => $this->getEndpointUrl($request)
            ]);
            $client->setConfig($config);
            $client->setUrlEncodeBody(false);
            $client->setParameterPost($requestParams->toArray());

            $rawResponse = $client->request($request->getMethod());
            $rawResponse = $rawResponse->getBody();
        } catch (\Zend_Http_Exception $e) {
            $this->logger->debug('REQUEST: [Endpoint: ' . $request->getEndpoint() . '] [Parameters: ' . $requestParams->toJson());
            $this->logger->debug('HTTP Request ERROR: ' . $e->getMessage());

            throw new LocalizedException(
                __('Something went wrong while doing a request to DPD service. Please contact system administrator for more information.')
            );
        }

        if ($this->apiConfig->isDebugMode()) {
            $this->logger->debug('REQUEST: [Endpoint: ' . $request->getEndpoint() . '] [Parameters: ' . $requestParams->toJson());
            $this->logger->debug('RESPONSE: ' . \substr($rawResponse, 0, 1000));
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

        return $apiUrl . self::API_METHOD_PATH . $request->getEndpoint();
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
            self::PARAM_AUTH_PASSWORD => $password,
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
            'EshopVersion' => 'Magento ' . $this->productMetadata->getVersion(),
        ];
    }
}