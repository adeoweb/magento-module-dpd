<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Carrier\Method;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Model\Carrier\Method\Pickup;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;

class PickupTest extends AbstractTest
{
    /**
     * @var MockObject|\AdeoWeb\Dpd\Helper\Config\Serializer
     */
    private $serializerMock;

    /**
     * @var Pickup
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

    /**
     * @var MockObject
     */
    private $pickupPointRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->rateMethodMock = $this->createPartialMock(\Magento\Quote\Model\Quote\Address\RateResult\Method::class, [
            'setPrice',
        ]);
        $this->rateRequestMock = $this->createMock(\Magento\Quote\Model\Quote\Address\RateRequest::class);
        $this->validatorMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\ValidatorInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->requestMock = $this->createMock(Http::class);
        $this->restrictionsConfig = $this->createMock(\AdeoWeb\Dpd\Config\Restrictions::class);
        $this->pickupPointRepositoryMock = $this->createMock(PickupPointRepositoryInterface::class);
        $this->serializerMock = $this->createMock(\AdeoWeb\Dpd\Helper\Config\Serializer::class);

        $rateMethodFactoryMock = $this->createConfiguredMock(
            \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory::class,
            ['create' => $this->rateMethodMock]
        );

        $carrierConfig = $this->objectManager->getObject(Config::class);

        $this->subject = $this->objectManager->getObject(Pickup::class, [
            'rateMethodFactory' => $rateMethodFactoryMock,
            'scopeConfig' => $this->scopeConfigMock,
            'request' => $this->requestMock,
            'carrierConfig' => $carrierConfig,
            'serializer' => $this->serializerMock,
            'restrictionsConfig' => null,
            'pickupPointRepository' => $this->pickupPointRepositoryMock,
            'validators' => [$this->validatorMock],
        ]);
        $this->subject->setRequest($this->rateRequestMock);
    }

    public function testGetRateResult()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(['request' => $this->rateRequestMock, 'method_code' => 'pickup'])
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->withConsecutive(
                ['carriers/dpd/title'],
                ['carriers/dpd/pickup/name'],
                ['carriers/dpd/pickup/price']
            )
            ->willReturnOnConsecutiveCalls('dpd', 'classic', 10, 20);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/pickup/free_shipping_enable')
            ->willReturn(0);

        $result = $this->subject->getRateResult();

        $this->assertEquals('dpd', $result->getData('carrier'));
    }

    public function testGetRateResultWithFreeshipping()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(['request' => $this->rateRequestMock, 'method_code' => 'pickup'])
            ->willReturn(true);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->withConsecutive(
                ['carriers/dpd/title'],
                ['carriers/dpd/pickup/name'],
                ['carriers/dpd/pickup/free_shipping_subtotal'],
                ['carriers/dpd/pickup/free_shipping_subtotal']
            )
            ->willReturnOnConsecutiveCalls('dpd', 'classic', 10, 10);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/pickup/free_shipping_enable')
            ->willReturn(1);

        $this->rateRequestMock->expects($this->atLeastOnce())
            ->method('__call')
            ->with('getPackageValueWithDiscount')
            ->willReturn(20);

        $result = $this->subject->getRateResult();

        $this->assertEquals('dpd', $result->getData('carrier'));
    }

    public function testProcessShipmentRequest()
    {
        $createShipmentRequestMock = $this->createMock(CreateShipmentRequest::class);
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $paymentMock = $this->createPartialMock(DataObject::class, []);
        $paymentMock->setData('method', 'cashondelivery');

        $orderMock = $this->createMock(\Magento\Sales\Model\Order::class);
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
                    'weight_units' => 'KG',
                ],
            ],
        ]);

        $result = $this->subject->processShipmentRequest($createShipmentRequestMock, $requestMock);

        $this->assertInstanceOf(CreateShipmentRequest::class, $result);
    }

    public function testProcessShipmentRequestWithDeliveryOptions()
    {
        $createShipmentRequestMock = $this->createMock(CreateShipmentRequest::class);
        $requestMock = $this->createPartialMock(DataObject::class, []);

        $paymentMock = $this->createPartialMock(DataObject::class, []);
        $paymentMock->setData('method', 'cashondelivery');

        $orderMock = $this->createMock(\Magento\Sales\Model\Order::class);
        $orderMock->expects($this->atleastOnce())
            ->method('getPayment')
            ->willReturn($paymentMock);
        $orderMock->expects($this->atleastOnce())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"api_id": 1}');

        $this->serializerMock->expects($this->any())
            ->method('unserialize')
            ->will($this->returnValueMap([['{"api_id": 1}', ['api_id' => 1]]]));

        $orderShipmentMock = $this->createPartialMock(DataObject::class, []);
        $orderShipmentMock->setData('order', $orderMock);

        $requestMock->setData('order_shipment', $orderShipmentMock);
        $requestMock->setData('packages', [
            [
                'params' => [
                    'weight' => 10.00,
                    'weight_units' => 'KG',
                ],
            ],
        ]);

        $pickupPointMock = $this->createMock(PickupPointInterface::class);

        $this->pickupPointRepositoryMock->expects($this->once())
            ->method('getByApiId')
            ->with('1')
            ->willReturn($pickupPointMock);

        $pickupPointMock->method('getCountry')->willReturn('some country');
        $pickupPointMock->method('getCity')->willReturn('some city');
        $pickupPointMock->method('getStreet')->willReturn('some street');
        $pickupPointMock->method('getPostcode')->willReturn('some postcode');

        $result = $this->subject->processShipmentRequest($createShipmentRequestMock, $requestMock);

        $this->assertInstanceOf(CreateShipmentRequest::class, $result);
        $this->assertEquals('some country', $requestMock->getData('recipient_address_country_code'));
        $this->assertEquals('some city', $requestMock->getData('recipient_address_city'));
        $this->assertEquals('some street', $requestMock->getData('recipient_address_street'));
        $this->assertEquals('some postcode', $requestMock->getData('recipient_address_postal_code'));
    }

    public function testValidateDeliveryOptionsWithInvalidPickupPointId()
    {
        $deliveryOptions = new DataObject(['api_id' => null]);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Please select DPD pickup point.');

        return $this->subject->validateDeliveryOptions($deliveryOptions);
    }

    public function testValidateDeliveryOptions()
    {
        $deliveryOptions = new DataObject(['api_id' => 1]);

        $this->assertTrue($this->subject->validateDeliveryOptions($deliveryOptions));
    }
}
