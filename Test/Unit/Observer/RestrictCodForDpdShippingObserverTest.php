<?php

namespace AdeoWeb\Dpd\Test\Unit\Observer;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Api\PickupPointRepositoryInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Observer\RestrictCodForDpdShippingObserver;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;

class RestrictCodForDpdShippingObserverTest extends AbstractTest
{
    /**
     * @var RestrictCodForDpdShippingObserver
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $observerMock;

    /**
     * @var MockObject
     */
    private $eventMock;

    /**
     * @var MockObject
     */
    private $pickupPointRepositoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->observerMock = $this->createMock(\Magento\Framework\Event\Observer::class);
        $this->eventMock = $this->createMock(\Magento\Framework\Event::class);

        $this->observerMock->expects($this->atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->eventMock);

        $serializer = $this->objectManager->getObject(\AdeoWeb\Dpd\Helper\Config\Serializer::class);
        $this->pickupPointRepositoryMock = $this->createMock(PickupPointRepositoryInterface::class);

        $this->subject = $this->objectManager->getObject(RestrictCodForDpdShippingObserver::class, [
            'serializer' => $serializer,
            'pickupPointRepository' => $this->pickupPointRepositoryMock
        ]);
    }

    public function testExecuteWithoutQuote()
    {
        $result = $this->subject->execute($this->observerMock);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteWithNonCODPaymentMethod()
    {
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $resultMock = $this->createMock(\Magento\Framework\DataObject::class);
        $paymentMethodInstanceMock = $this->createMock(\Magento\Payment\Model\Method\AbstractMethod::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getData')
            ->withConsecutive(['quote'], ['result'], ['method_instance'])
            ->willReturn($quoteMock, $resultMock, $paymentMethodInstanceMock);

        $paymentMethodInstanceMock->expects($this->once())
            ->method('getCode')
            ->willReturn('test_method');

        $result = $this->subject->execute($this->observerMock);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteWithInvalidShippingMethod()
    {
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $resultMock = $this->createMock(\Magento\Framework\DataObject::class);
        $paymentMethodInstanceMock = $this->createMock(\Magento\Payment\Model\Method\AbstractMethod::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getData')
            ->withConsecutive(['quote'], ['result'], ['method_instance'])
            ->willReturn($quoteMock, $resultMock, $paymentMethodInstanceMock);

        $paymentMethodInstanceMock->expects($this->once())
            ->method('getCode')
            ->willReturn('cashondelivery');

        $addressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($addressMock);

        $addressMock->expects($this->once())
            ->method('getShippingMethod')
            ->willReturn('dpd_classic');

        $result = $this->subject->execute($this->observerMock);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteWithInvalidDeliveryOptions()
    {
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $resultMock = $this->createMock(\Magento\Framework\DataObject::class);
        $paymentMethodInstanceMock = $this->createMock(\Magento\Payment\Model\Method\AbstractMethod::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getData')
            ->withConsecutive(['quote'], ['result'], ['method_instance'])
            ->willReturn($quoteMock, $resultMock, $paymentMethodInstanceMock);

        $paymentMethodInstanceMock->expects($this->once())
            ->method('getCode')
            ->willReturn('cashondelivery');

        $addressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($addressMock);

        $addressMock->expects($this->once())
            ->method('getShippingMethod')
            ->willReturn('dpd_pickup');

        $quoteMock->expects($this->once())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"delivery_time":1}');

        $result = $this->subject->execute($this->observerMock);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecuteWithPickupPointException()
    {
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $resultMock = $this->createMock(\Magento\Framework\DataObject::class);
        $paymentMethodInstanceMock = $this->createMock(\Magento\Payment\Model\Method\AbstractMethod::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getData')
            ->withConsecutive(['quote'], ['result'], ['method_instance'])
            ->willReturn($quoteMock, $resultMock, $paymentMethodInstanceMock);

        $paymentMethodInstanceMock->expects($this->once())
            ->method('getCode')
            ->willReturn('cashondelivery');

        $addressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($addressMock);

        $addressMock->expects($this->once())
            ->method('getShippingMethod')
            ->willReturn('dpd_pickup');

        $quoteMock->expects($this->once())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"pickup_point_id":1}');

        $this->pickupPointRepositoryMock->expects($this->once())
            ->method('getById')
            ->with('1')
            ->willThrowException(new NoSuchEntityException(__('No such entity')));

        $resultMock->expects($this->atLeastOnce())
            ->method('setData')
            ->with('is_available', false);

        $this->subject->execute($this->observerMock);
    }

    public function testExecuteWithNonCODPickupPoint()
    {
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $resultMock = $this->createMock(\Magento\Framework\DataObject::class);
        $paymentMethodInstanceMock = $this->createMock(\Magento\Payment\Model\Method\AbstractMethod::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getData')
            ->withConsecutive(['quote'], ['result'], ['method_instance'])
            ->willReturn($quoteMock, $resultMock, $paymentMethodInstanceMock);

        $paymentMethodInstanceMock->expects($this->once())
            ->method('getCode')
            ->willReturn('cashondelivery');

        $addressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($addressMock);

        $addressMock->expects($this->once())
            ->method('getShippingMethod')
            ->willReturn('dpd_pickup');

        $quoteMock->expects($this->once())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"pickup_point_id":1}');

        $pickupPointMock = $this->createMock(PickupPointInterface::class);

        $this->pickupPointRepositoryMock->expects($this->once())
            ->method('getById')
            ->with('1')
            ->willReturn($pickupPointMock);

        $pickupPointMock->expects($this->atLeastOnce())
            ->method('getApiId')
            ->willReturn('LT100000');

        $resultMock->expects($this->atLeastOnce())
            ->method('setData')
            ->with('is_available', false);

        $this->subject->execute($this->observerMock);
    }

    public function testExecute()
    {
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $resultMock = $this->createMock(\Magento\Framework\DataObject::class);
        $paymentMethodInstanceMock = $this->createMock(\Magento\Payment\Model\Method\AbstractMethod::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getData')
            ->withConsecutive(['quote'], ['result'], ['method_instance'])
            ->willReturn($quoteMock, $resultMock, $paymentMethodInstanceMock);

        $paymentMethodInstanceMock->expects($this->once())
            ->method('getCode')
            ->willReturn('cashondelivery');

        $addressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);

        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($addressMock);

        $addressMock->expects($this->once())
            ->method('getShippingMethod')
            ->willReturn('dpd_pickup');

        $quoteMock->expects($this->once())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"pickup_point_id":1}');

        $pickupPointMock = $this->createMock(PickupPointInterface::class);

        $this->pickupPointRepositoryMock->expects($this->once())
            ->method('getById')
            ->with('1')
            ->willReturn($pickupPointMock);

        $pickupPointMock->expects($this->atLeastOnce())
            ->method('getApiId')
            ->willReturn('LT900000');

        $resultMock->expects($this->atLeastOnce())
            ->method('setData')
            ->with('is_available', true);

        $this->subject->execute($this->observerMock);
    }
}
