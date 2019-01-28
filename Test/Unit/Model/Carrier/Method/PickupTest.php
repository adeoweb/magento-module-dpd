<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Carrier\Method;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Helper\Config;
use AdeoWeb\Dpd\Model\Carrier\Method\Pickup;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;

class PickupTest extends AbstractTest
{
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

    public function setUp()
    {
        parent::setUp();

        $this->rateMethodMock = $this->createPartialMock(\Magento\Quote\Model\Quote\Address\RateResult\Method::class, [
            'setPrice',
        ]);
        $this->rateRequestMock = $this->createMock(\Magento\Quote\Model\Quote\Address\RateRequest::class);
        $this->validatorMock = $this->createMock(\AdeoWeb\Dpd\Model\Carrier\ValidatorInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->requestMock = $this->createMock(Http::class);
        $this->restrictionsConfig = $this->createMock(\AdeoWeb\Dpd\Config\Classic\Restrictions::class);
        $this->pickupPointRepositoryMock = $this->createMock(PickupPointRepositoryInterface::class);

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
            'restrictionsConfig' => $this->restrictionsConfig,
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
            ->withConsecutive(['carriers/dpd/title'], ['carriers/dpd/pickup/name'],
                ['carriers/dpd/pickup/price'])
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
            ->withConsecutive(['carriers/dpd/title'], ['carriers/dpd/pickup/name'],
                ['carriers/dpd/pickup/free_shipping_subtotal'],['carriers/dpd/pickup/free_shipping_subtotal'])
            ->willReturnOnConsecutiveCalls('dpd', 'classic', 10, 10);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/pickup/free_shipping_enable')
            ->willReturn(1);

        $this->rateRequestMock->expects($this->atLeastOnce())
            ->method('__call')
            ->with('getPackageValue')
            ->willReturn(20);

        $result = $this->subject->getRateResult();

        $this->assertEquals('dpd', $result->getData('carrier'));
    }

    public function testProcessShipmentRequest()
    {
        $createShipmentRequestMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest::class);
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

        $this->assertInstanceOf(\AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest::class, $result);
    }

    public function testProcessShipmentRequestWithDeliveryOptions()
    {
        $createShipmentRequestMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest::class);
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
            ->willReturn('{"pickup_point_id": 1}');

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
            ->method('getById')
            ->with('1')
            ->willReturn($pickupPointMock);

        $result = $this->subject->processShipmentRequest($createShipmentRequestMock, $requestMock);

        $this->assertInstanceOf(\AdeoWeb\Dpd\Model\Service\Dpd\Request\CreateShipmentRequest::class, $result);
    }
}
