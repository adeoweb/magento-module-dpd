<?php

namespace AdeoWeb\Dpd\Model\Service;

use AdeoWeb\Dpd\Config\Api;
use AdeoWeb\Dpd\Model\Service\Dpd\ResponseFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Module\PackageInfo;
use Magento\Framework\Validator\Url;
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
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ZendClientFactory
     */
    private $httpClientFactory;

    /**
     * @var Url
     */
    private $urlValidator;

    public function __construct(
        Api $apiConfig,
        ResponseFactory $responseFactory,
        LoggerInterface $logger,
        PackageInfo $packageInfo,
        ProductMetadataInterface $productMetadata,
        ZendClientFactory $httpClientFactory,
        Url $urlValidator
    ) {
        $this->apiConfig = $apiConfig;
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->packageInfo = $packageInfo;
        $this->productMetadata = $productMetadata;
        $this->httpClientFactory = $httpClientFactory;
        $this->urlValidator = $urlValidator;
    }

    /**
     * @param RequestInterface $request
     * @return null|ResponseInterface|string
     * @throws LocalizedException
     */
    public function call(RequestInterface $request)
    {
        $config = [
            'verifypeer' => false,
            'timeout' => 300,
        ];

        $requestParams = $this->getRequestParams($request);

        try {
            $client = $this->httpClientFactory->create([
                'uri' => $this->getEndpointUrl($request),
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
            $this->logCall($request->getEndpoint(), $requestParams, $rawResponse);
        }

        if ($request->isFile()) {
            return $rawResponse;
        }

        $rawResponse = \json_decode($rawResponse, true);

        return $this->responseFactory->create($rawResponse);
    }

    private function logCall(string $endpoint, DataObject $requestParams, string $rawResponse)
    {
        $requestParamsCopy = new DataObject($requestParams->getData());

        if ($requestParamsCopy->hasData(self::PARAM_AUTH_PASSWORD)) {
            $requestParamsCopy->setData(self::PARAM_AUTH_PASSWORD, '*****');
        }

        $this->logger->debug('REQUEST: [Endpoint: ' . $endpoint . '] [Parameters: ' . $requestParamsCopy->toJson());
        $this->logger->debug('RESPONSE: ' . \substr($rawResponse, 0, 1000));
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getEndpointUrl(RequestInterface $request)
    {
        $apiUrl = $this->apiConfig->getUrl();

        if (!$apiUrl || !$this->urlValidator->isValid($apiUrl)) {
            throw new \RuntimeException('DPD Module is not configured. API URL is missing or invalid.');
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
        $moduleVersion = $this->packageInfo->getVersion('AdeoWeb_Dpd');

        return [
            'PluginVersion' => $moduleVersion,
            'EshopVersion' => 'Magento ' . $this->productMetadata->getVersion(),
        ];
    }
}
