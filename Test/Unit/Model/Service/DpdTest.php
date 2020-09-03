<?php

namespace Model\Service;

use AdeoWeb\Dpd\Config\Api;
use AdeoWeb\Dpd\Model\Service\Dpd;
use AdeoWeb\Dpd\Model\Service\RequestInterface;
use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Validator\Url;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Zend_Http_Exception;
use Zend_Http_Response;

class DpdTest extends AbstractTest
{
    /**
     * @var Dpd
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $apiConfigMock;

    /**
     * @var MockObject
     */
    private $httpClientMock;

    /**
     * @var MockObject
     */
    private $loggerMock;

    /**
     * @var MockObject
     */
    private $responseMock;

    /**
     * @var MockObject
     */
    private $responseFactoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->apiConfigMock = $this->createMock(Api::class);
        $this->httpClientMock = $this->createMock(ZendClient::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->responseMock = $this->createMock(ResponseInterface::class);

        $this->responseFactoryMock = $this->createConfiguredMock(Dpd\ResponseFactory::class, [
            'create' => $this->responseMock
        ]);

        $httpClientFactory = $this->createConfiguredMock(ZendClientFactory::class, [
            'create' => $this->httpClientMock
        ]);

        $urlValidator = $this->createConfiguredMock(Url::class, [
            'isValid' => true
        ]);

        $this->subject = $this->objectManager->getObject(Dpd::class, [
            'apiConfig' => $this->apiConfigMock,
            'httpClientFactory' => $httpClientFactory,
            'logger' => $this->loggerMock,
            'responseFactory' => $this->responseFactoryMock,
            'urlValidator' => $urlValidator
        ]);
    }

    public function testCallWithApiCredentialsException()
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('DPD Module is not configured. API Username or/and password missing');

        return $this->subject->call($requestMock);
    }

    public function testCallWithApiUrlMissingException()
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $requestMock->method('getParams')->willReturn([]);

        $this->apiConfigMock->method('getUsername')->willReturn('testUsername');
        $this->apiConfigMock->method('getPassword')->willReturn('testPass');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('DPD Module is not configured. API URL is missing or invalid.');

        return $this->subject->call($requestMock);
    }

    public function testCallWithRequestException()
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $requestMock->method('getParams')->willReturn([]);

        $this->apiConfigMock->method('getUsername')->willReturn('testUsername');
        $this->apiConfigMock->method('getPassword')->willReturn('testPass');
        $this->apiConfigMock->method('getUrl')->willReturn('http://testapi.com');

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new Zend_Http_Exception('Invalid data'));

        $this->loggerMock->expects($this->atleastOnce())
            ->method('debug');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Something went wrong while doing a request to DPD service. Please contact system administrator for more information.');

        return $this->subject->call($requestMock);
    }

    public function testCallWithRawRequest()
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('isFile')->willReturn(true);

        $this->apiConfigMock->method('getUsername')->willReturn('testUsername');
        $this->apiConfigMock->method('getPassword')->willReturn('testPass');
        $this->apiConfigMock->method('getUrl')->willReturn('http://testapi.com');
        $this->apiConfigMock->method('isDebugMode')->willReturn(false);

        $responseMock = $this->createMock(Zend_Http_Response::class);
        $responseMock->method('getBody')->willReturn('Test Response');

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $this->assertEquals('Test Response', $this->subject->call($requestMock));
    }

    public function testCall()
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('isFile')->willReturn(false);

        $this->apiConfigMock->method('getUsername')->willReturn('testUsername');
        $this->apiConfigMock->method('getPassword')->willReturn('testPass');
        $this->apiConfigMock->method('getUrl')->willReturn('http://testapi.com');
        $this->apiConfigMock->method('isDebugMode')->willReturn(false);

        $responseMock = $this->createMock(Zend_Http_Response::class);
        $responseMock->method('getBody')->willReturn('{"a": "b"}');

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->willReturn($responseMock);

        $this->responseFactoryMock->expects($this->once())
            ->method('create')
            ->with(['a' => 'b']);

        $result = $this->subject->call($requestMock);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testCallHidePassword()
    {
        $requestMock = $this->createMock(RequestInterface::class);

        $requestMock->method('getParams')->willReturn([]);
        $requestMock->method('getEndpoint')->willReturn('http://testapi.com/something');

        $this->apiConfigMock->method('getUsername')->willReturn('testUsername');
        $this->apiConfigMock->method('getPassword')->willReturn('testPass');
        $this->apiConfigMock->method('getUrl')->willReturn('http://testapi.com');
        $this->apiConfigMock->method('isDebugMode')->willReturn(true);

        $responseMock = $this->createMock(Zend_Http_Response::class);
        $responseMock->method('getBody')->willReturn('{"a": "b"}');

        $this->httpClientMock->method('request')->willReturn($responseMock);

        $this->loggerMock->expects($this->at(0))
            ->method('debug')
            ->with('REQUEST: [Endpoint: http://testapi.com/something] [Parameters: {"username":"testUsername","password":"*****","PluginVersion":null,"EshopVersion":"Magento "}');

        $result = $this->subject->call($requestMock);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
