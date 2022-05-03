<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Carrier\Method;

use AdeoWeb\Dpd\Config\Restrictions;
use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use AdeoWeb\Dpd\Model\Carrier\Method\Classic;
use AdeoWeb\Dpd\Model\Carrier\ValidatorInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;

class ClassicTest extends AbstractTest
{
    /**
     * @var Classic
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $rateMethodMock;

    /**
     * @var MockObject
     */
    private $rateRequestMock;

    /**
     * @var MockObject
     */
    private $validatorMock;

    /**
     * @var MockObject
     */
    private $scopeConfigMock;

    /**
     * @var MockObject
     */
    private $requestMock;

    /**
     * @var MockObject
     */
    private $restrictionsConfig;

    public function setUp(): void
    {
        parent::setUp();

        $this->rateMethodMock = $this->createPartialMock(Method::class, ['setPrice',]);
        $this->rateRequestMock = $this->createMock(RateRequest::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->requestMock = $this->createMock(Http::class);
        $this->restrictionsConfig = $this->createMock(Restrictions::class);

        $rateMethodFactoryMock = $this->createConfiguredMock(MethodFactory::class, ['create' => $this->rateMethodMock]);
        $carrierConfig = $this->objectManager->getObject(Config::class);

        $serializerMock = $this->createMock(Serializer::class);
        $serializerMock->expects($this->any())->method('unserialize')->will($this->returnValueMap([
            ['{"delivery_time": 1}', ['delivery_time' => 1]]
        ]));

        $this->subject = $this->objectManager->getObject(Classic::class, [
            'rateMethodFactory' => $rateMethodFactoryMock,
            'scopeConfig' => $this->scopeConfigMock,
            'request' => $this->requestMock,
            'carrierConfig' => $carrierConfig,
            'serializer' => $serializerMock,
            'restrictionsConfig' => $this->restrictionsConfig,
            'validators' => [$this->validatorMock],
        ]);
        $this->subject->setRequest($this->rateRequestMock);
    }

    public function testValidateWithException()
    {
        $wrongValidator = $this->createMock(DataObject::class);


        $subject = $this->objectManager->getObject(Classic::class, [
            'validators' => [$wrongValidator],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Validator must be instance of AdeoWeb\Dpd\Model\Carrier\ValidatorInterface');

        return $subject->validate();
    }

    public function testValidateWithValidatorFail()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(['request' => $this->rateRequestMock, 'method_code' => 'classic'])
            ->willReturn(false);

        $this->assertFalse($this->subject->validate());
    }

    public function testValidate()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(['request' => $this->rateRequestMock, 'method_code' => 'classic'])
            ->willReturn(true);

        $this->assertTrue($this->subject->validate());
    }

    public function testGetRateResultWithValidateFail()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(['request' => $this->rateRequestMock, 'method_code' => 'classic'])
            ->willReturn(false);

        $result = $this->subject->getRateResult();

        $this->assertEquals(null, $result->getData('carrier'));
    }

    public function testGetRateResult()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(['request' => $this->rateRequestMock, 'method_code' => 'classic'])
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->withConsecutive(
                ['carriers/dpd/title'],
                ['carriers/dpd/classic/name'],
                ['carriers/dpd/classic/free_shipping_subtotal'],
                ['carriers/dpd/classic/free_shipping_subtotal']
            )
            ->willReturnOnConsecutiveCalls('dpd', 'classic', 20, 20);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/classic/free_shipping_enable')
            ->willReturn(1);

        $this->rateRequestMock->expects($this->atLeastOnce())
            ->method('__call')
            ->with('getPackageValueWithDiscount')
            ->willReturn(10);

        $this->restrictionsConfig->expects($this->atleastOnce())
            ->method('getByCountryWeight')
            ->willReturn('20');

        $result = $this->subject->getRateResult();

        $this->assertEquals('dpd', $result->getData('carrier'));
    }

    public function testGetRateResultWithoutPriceRestrictions()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(['request' => $this->rateRequestMock, 'method_code' => 'classic'])
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->withConsecutive(
                ['carriers/dpd/title'],
                ['carriers/dpd/classic/name'],
                ['carriers/dpd/classic/free_shipping_subtotal'],
                ['carriers/dpd/classic/free_shipping_subtotal']
            )
            ->willReturnOnConsecutiveCalls('dpd', 'classic', 10, 20);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/classic/free_shipping_enable')
            ->willReturn(true);

        $this->rateRequestMock->expects($this->atLeastOnce())
            ->method('__call')
            ->with('getPackageValueWithDiscount')
            ->willReturn(10);

        $result = $this->subject->getRateResult();

        $this->assertEquals('dpd', $result->getData('carrier'));
    }

    public function testProcessShipmentRequest()
    {
        $createShipmentRequestMock = $this->createMock(CreateShipmentRequest::class);
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $paymentMock = $this->createPartialMock(DataObject::class, []);
        $paymentMock->setData('method', 'cashondelivery');

        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->atleastOnce())
            ->method('getPayment')
            ->willReturn($paymentMock);

        $orderShipmentMock = $this->createPartialMock(DataObject::class, []);
        $orderShipmentMock->setData('order', $orderMock);

        $requestMock->setData('order_shipment', $orderShipmentMock);
        $requestMock->setData('packages', [
            [
                'params' => [
                    'weight' => 10.00,
                    'weight_units' => 'KG'
                ]
            ]
        ]);

        $this->requestMock
            ->method('getParam')
            ->withConsecutive(['dpd_include_return_labels'], ['dpd_document_return_service'])
            ->willReturnOnConsecutiveCalls('1', '1');

        $result = $this->subject->processShipmentRequest($createShipmentRequestMock, $requestMock);

        $this->assertInstanceOf(CreateShipmentRequest::class, $result);
    }

    public function testProcessShipmentRequestWithDeliveryTime()
    {
        $createShipmentRequestMock = $this->createMock(CreateShipmentRequest::class);
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $paymentMock = $this->createPartialMock(DataObject::class, []);
        $paymentMock->setData('method', 'cashondelivery');

        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->atleastOnce())
            ->method('getPayment')
            ->willReturn($paymentMock);
        $orderMock->expects($this->atleastOnce())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"delivery_time": 1}');

        $orderShipmentMock = $this->createPartialMock(DataObject::class, []);
        $orderShipmentMock->setData('order', $orderMock);

        $requestMock->setData('order_shipment', $orderShipmentMock);
        $requestMock->setData('packages', [
            [
                'params' => [
                    'weight' => 10.00,
                    'weight_units' => 'KG'
                ]
            ]
        ]);

        $this->requestMock
            ->method('getParam')
            ->withConsecutive(['dpd_include_return_labels'], ['dpd_document_return_service'])
            ->willReturnOnConsecutiveCalls('1', '1');

        $result = $this->subject->processShipmentRequest($createShipmentRequestMock, $requestMock);

        $this->assertInstanceOf(CreateShipmentRequest::class, $result);
    }
}
