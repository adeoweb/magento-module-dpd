<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelPrintRequestFactory;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelStatusRequest;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelStatusRequestFactory;
use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Model\Carrier;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;
use Zend\Log\Filter\Mock;

class CarrierTest extends AbstractTest
{
    /**
     * @var Carrier
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $scopeConfigMock;

    /**
     * @var MockObject
     */
    private $rateResultMock;

    /**
     * @var MockObject
     */
    private $methodFactoryPoolMock;

    /**
     * @var MockObject
     */
    private $parcelStatusRequestMock;

    /**
     * @var MockObject
     */
    private $trackStatusMock;

    /**
     * @var MockObject
     */
    private $carrierServiceMock;

    /**
     * @var MockObject
     */
    private $shipmentRequestMock;

    /**
     * @var MockObject
     */
    private $parcelPrintRequestMock;

    public function setUp()
    {
        parent::setUp();

        $this->scopeConfigMock = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $this->rateResultMock = $this->createPartialMock(\Magento\Shipping\Model\Rate\Result::class, []);
        $this->methodFactoryPoolMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\MethodFactoryPool::class);
        $this->carrierServiceMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd::class);
        $this->parcelStatusRequestMock = $this->createMock(ParcelStatusRequest::class);
        $this->shipmentRequestMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest::class);
        $this->parcelPrintRequestMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelPrintRequest::class);

        $createShipmentRequestFactoryMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequestFactory::class);
        $createShipmentRequestFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->shipmentRequestMock);

        $parcelStatusRequestFactoryMock = $this->createMock(ParcelStatusRequestFactory::class);
        $parcelStatusRequestFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->parcelStatusRequestMock);

        $rateResultFactory = $this->createMock(\Magento\Shipping\Model\Rate\ResultFactory::class);
        $rateResultFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->rateResultMock);

        $this->trackStatusMock = $this->createMock(\Magento\Shipping\Model\Tracking\Result\Status::class);

        $trackStatusFactoryMock = $this->createMock(\Magento\Shipping\Model\Tracking\Result\StatusFactory::class);
        $trackStatusFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->trackStatusMock);

        $parcelPrintRequestFactoryMock = $this->createMock(ParcelPrintRequestFactory::class);
        $parcelPrintRequestFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->parcelPrintRequestMock);

        $this->subject = $this->objectManager->getObject(Carrier::class, [
            'scopeConfig' => $this->scopeConfigMock,
            'rateFactory' => $rateResultFactory,
            'methodFactoryPool' => $this->methodFactoryPoolMock,
            'parcelStatusRequestFactory' => $parcelStatusRequestFactoryMock,
            'trackStatusFactory' => $trackStatusFactoryMock,
            'dpdService' => $this->carrierServiceMock,
            'createShipmentRequestFactory' => $createShipmentRequestFactoryMock,
            'parcelPrintRequestFactory' => $parcelPrintRequestFactoryMock,
        ]);
    }

    public function testCollectRatesWithDisabledCarrier()
    {
        $rateRequestMock = $this->createMock(\Magento\Quote\Model\Quote\Address\RateRequest::class);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/active')
            ->willReturn(false);

        $result = $this->subject->collectRates($rateRequestMock);

        $this->assertFalse($result);
    }

    public function testCollectRates()
    {
        $rateRequestMock = $this->createMock(\Magento\Quote\Model\Quote\Address\RateRequest::class);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/active')
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->with('carriers/dpd/allowed_methods')
            ->willReturn('classic,test');

        $classicMethodRateResultMock = $this->createMock(\Magento\Quote\Model\Quote\Address\RateResult\AbstractResult::class);

        $classicMethodMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\Method\Classic::class);
        $classicMethodMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);
        $classicMethodMock->expects($this->once())
            ->method('getRateResult')
            ->willReturn($classicMethodRateResultMock);

        $this->methodFactoryPoolMock->expects($this->atLeastOnce())
            ->method('getInstance')
            ->withConsecutive(['classic'], ['test'])
            ->willReturn($classicMethodMock, null);

        $result = $this->subject->collectRates($rateRequestMock);

        $rates = $result->getAllRates();
        $this->assertSame($classicMethodRateResultMock, $rates[0]);
    }

    public function testGetTrackingInfoWithError()
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('hasError')
            ->willReturn(false);

        $result = $this->subject->getTrackingInfo('LZ485484');

        $this->assertInstanceOf(\Magento\Shipping\Model\Tracking\Result\Status::class, $result);
    }

    public function testRequestToShipmentWithNoPackages()
    {
        $request = $this->createPartialMock(DataObject::class, []);

        $request->setPackages(null);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('No packages for request');

        return $this->subject->requestToShipment($request);
    }

    public function testRequestToShipmentWithNonExistingMethod()
    {
        $request = $this->createPartialMock(DataObject::class, []);

        $request->setPackages(['test']);
        $request->setStoreId(0);
        $request->setShippingMethod('test');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('DPD Carrier method "test" does not exist');

        return $this->subject->requestToShipment($request);
    }

    public function testRequestToShipmentWithNonShipmentRequestResponseError()
    {
        $request = $this->createPartialMock(DataObject::class, []);

        $request->setPackages(['test']);
        $request->setStoreId(0);
        $request->setShippingMethod('classic');

        $classicMethodMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\Method\Classic::class);
        $classicMethodMock->expects($this->once())
            ->method('processShipmentRequest')
            ->with($this->shipmentRequestMock, $request)
            ->willReturn($this->shipmentRequestMock);

        $this->methodFactoryPoolMock->expects($this->once())
            ->method('getInstance')
            ->with('classic', null)
            ->willReturn($classicMethodMock);

        $responseMock = $this->createMock(ResponseInterface::class);
        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->with($this->shipmentRequestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('hasError')
            ->willReturn(true);
        $responseMock->expects($this->once())
            ->method('getErrorMessage')
            ->willReturn('Bad response');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bad response');

        return $this->subject->requestToShipment($request);
    }

    public function testProccessAdditionalValidation()
    {
        $this->assertTrue($this->subject->proccessAdditionalValidation(new DataObject()));
    }

    public function testRequestToShipmentWithParcelPrintException()
    {
        $request = $this->createPartialMock(DataObject::class, []);

        $request->setPackages(['test']);
        $request->setStoreId(0);
        $request->setShippingMethod('classic');

        $classicMethodMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\Method\Classic::class);
        $classicMethodMock->expects($this->once())
            ->method('processShipmentRequest')
            ->with($this->shipmentRequestMock, $request)
            ->willReturn($this->shipmentRequestMock);

        $this->methodFactoryPoolMock->expects($this->once())
            ->method('getInstance')
            ->with('classic', null)
            ->willReturn($classicMethodMock);

        $shipmentRequestResponseMock = $this->createMock(ResponseInterface::class);

        $this->carrierServiceMock->expects($this->atLeastOnce())
            ->method('call')
            ->withConsecutive([$this->shipmentRequestMock], [$this->parcelPrintRequestMock])
            ->willReturnOnConsecutiveCalls($shipmentRequestResponseMock,
                $this->throwException(new LocalizedException(__('Invalid response'))));

        $shipmentRequestResponseMock->expects($this->once())
            ->method('hasError')
            ->willReturn(false);

        $shipmentRequestResponseMock->expects($this->once())
            ->method('getBody')
            ->with('pl_number')
            ->willReturn(['311515', '487848', '3368484']);

        $result = $this->subject->requestToShipment($request);

        $this->assertArrayHasKey('errors', $result);
    }

    public function testRequestToShipment()
    {
        $request = $this->createPartialMock(DataObject::class, []);

        $request->setPackages(['test']);
        $request->setStoreId(0);
        $request->setShippingMethod('classic');

        $classicMethodMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\Method\Classic::class);
        $classicMethodMock->expects($this->once())
            ->method('processShipmentRequest')
            ->with($this->shipmentRequestMock, $request)
            ->willReturn($this->shipmentRequestMock);

        $this->methodFactoryPoolMock->expects($this->once())
            ->method('getInstance')
            ->with('classic', null)
            ->willReturn($classicMethodMock);

        $shipmentRequestResponseMock = $this->createMock(ResponseInterface::class);
        $parcelPrintRequestResponseMock = 'PDFCONTENT';
        $this->carrierServiceMock->expects($this->atLeastOnce())
            ->method('call')
            ->withConsecutive([$this->shipmentRequestMock], [$this->parcelPrintRequestMock])
            ->willReturnOnConsecutiveCalls($shipmentRequestResponseMock, $parcelPrintRequestResponseMock);

        $shipmentRequestResponseMock->expects($this->once())
            ->method('hasError')
            ->willReturn(false);

        $shipmentRequestResponseMock->expects($this->once())
            ->method('getBody')
            ->with('pl_number')
            ->willReturn(['311515', '487848', '3368484']);

        $result = $this->subject->requestToShipment($request);

        $this->assertArrayHasKey('info', $result);
    }
}