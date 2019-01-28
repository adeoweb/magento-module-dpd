<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Order;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Block\Adminhtml\Order\ShippingAdditionalInfo;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use PHPUnit\Framework\MockObject\MockObject;

class ShippingAdditionalInfoTest extends AbstractTest
{
    /**
     * @var ShippingAdditionalInfo
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $registryMock;

    /**
     * @var MockObject
     */
    private $pickupPointRepositoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->registryMock = $this->createMock(\Magento\Framework\Registry::class);
        $this->pickupPointRepositoryMock = $this->createMock(\AdeoWeb\Dpd\Api\PickupPointRepositoryInterface::class);

        $carrierConfigMock = $this->objectManager->getObject(\AdeoWeb\Dpd\Helper\Config::class);

        $this->subject = $this->objectManager->getObject(ShippingAdditionalInfo::class, [
            'registry' => $this->registryMock,
            'carrierConfig' => $carrierConfigMock,
            'pickupPointRepository' => $this->pickupPointRepositoryMock
        ]);
    }

    public function testGetOrderWithRegistryOrder()
    {
        $orderMock = $this->createMock(OrderInterface::class);

        $this->registryMock->expects($this->atleastOnce())
            ->method('registry')
            ->with('current_order')
            ->willReturn($orderMock);

        $result = $this->subject->getOrder();
        $expectedResult = $orderMock;

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetOrderWithRegistryShipment()
    {
        $orderMock = $this->createMock(OrderInterface::class);

        $shipmentMock = $this->createMock(Shipment::class);
        $shipmentMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->registryMock->expects($this->atLeastOnce())
            ->method('registry')
            ->withConsecutive(['current_order'], ['current_shipment'])
            ->willReturnOnConsecutiveCalls(null, $shipmentMock);

        $result = $this->subject->getOrder();
        $expectedResult = $orderMock;

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetOrderWithRegistryCreditmemo()
    {
        $orderMock = $this->createMock(OrderInterface::class);

        $creditmemoMock = $this->createMock(Creditmemo::class);
        $creditmemoMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->registryMock->expects($this->atLeastOnce())
            ->method('registry')
            ->withConsecutive(['current_order'], ['current_shipment'], ['current_creditmemo'])
            ->willReturnOnConsecutiveCalls(null, null, $creditmemoMock);


        $result = $this->subject->getOrder();
        $expectedResult = $orderMock;

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetOrderWithRegistryInvoice()
    {
        $orderMock = $this->createMock(OrderInterface::class);

        $invoiceMock = $this->createMock(Invoice::class);
        $invoiceMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $this->registryMock->expects($this->atLeastOnce())
            ->method('registry')
            ->withConsecutive(['current_order'], ['current_shipment'], ['current_creditmemo'], ['current_invoice'])
            ->willReturnOnConsecutiveCalls(null, null, null, $invoiceMock);

        $result = $this->subject->getOrder();
        $expectedResult = $orderMock;

        $this->assertEquals($result, $expectedResult);
    }


    public function testGetOrderWithException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order is not set');

        return $this->subject->getOrder();
    }

    public function testGetAdditionalInfoWithIncorrectPickupPoint()
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->atLeastOnce())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"pickup_point_id": "2", "delivery_time": "1"}');

        $this->registryMock->expects($this->atleastOnce())
            ->method('registry')
            ->with('current_order')
            ->willReturn($orderMock);

        $this->pickupPointRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with('2')
            ->willThrowException(new NoSuchEntityException());

        $result = $this->subject->getAdditionalInfo();
        $expectedResult = array(
            0 =>
                array(
                    'label' => __('Delivery Time'),
                    'value' => '8:00 - 14:00',
                ),
        );

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetAdditionalInfo()
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->atLeastOnce())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn('{"pickup_point_id": "1", "delivery_time": "1"}');

        $this->registryMock->expects($this->atleastOnce())
            ->method('registry')
            ->with('current_order')
            ->willReturn($orderMock);

        $pickupPointMock = $this->createMock(PickupPointInterface::class);
        $pickupPointMock->expects($this->atLeastOnce())
            ->method('getApiId')
            ->willReturn('LT00000');
        $pickupPointMock->expects($this->atLeastOnce())
            ->method('getCompany')
            ->willReturn('TestCompany');

        $this->pickupPointRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with('1')
            ->willReturn($pickupPointMock);

        $result = $this->subject->getAdditionalInfo();
        $expectedResult = array(
            0 =>
                array(
                    'label' => __('Pickup Point'),
                    'value' => '(LT00000) TestCompany',
                ),
            1 =>
                array(
                    'label' => __('Delivery Time'),
                    'value' => '8:00 - 14:00',
                ),
        );

        $this->assertEquals($result, $expectedResult);
    }

    public function test_toHtmlWithNonApplicableCarrier()
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->atLeastOnce())
            ->method('getShippingMethod')
            ->willReturn('test_test');

        $this->registryMock->expects($this->atleastOnce())
            ->method('registry')
            ->with('current_order')
            ->willReturn($orderMock);

        $result = $this->subject->_toHtml();
        $expectedResult = '';

        $this->assertEquals($result, $expectedResult);
    }

    public function test_toHtml()
    {
        $orderMock = $this->createMock(Order::class);
        $orderMock->expects($this->atLeastOnce())
            ->method('getShippingMethod')
            ->willReturn('dpd_classic');

        $this->registryMock->expects($this->atleastOnce())
            ->method('registry')
            ->with('current_order')
            ->willReturn($orderMock);


        $this->subject->setTemplate('test.phtml');

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to a member function getRelativePath() on null');

        return $this->subject->_toHtml();
    }
}
