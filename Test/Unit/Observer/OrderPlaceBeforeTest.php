<?php

namespace AdeoWeb\Dpd\Test\Unit\Observer;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Observer\OrderPlaceBefore;
use PHPUnit\Framework\MockObject\MockObject;

class OrderPlaceBeforeTest extends AbstractTest
{
    /**
     * @var OrderPlaceBefore
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

    public function setUp()
    {
        parent::setUp();

        $this->observerMock = $this->createMock(\Magento\Framework\Event\Observer::class);
        $this->eventMock = $this->createMock(\Magento\Framework\Event::class);

        $this->observerMock->expects($this->atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->eventMock);

        $this->subject = $this->objectManager->getObject(OrderPlaceBefore::class);
    }

    public function testExecuteWithoutDeliveryOptions()
    {
        $orderMock = $this->createMock(\Magento\Sales\Model\Order::class);
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('__call')
            ->withConsecutive(['getOrder'], ['getQuote'])
            ->willReturn($orderMock, $quoteMock);

        $quoteMock->expects($this->once())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn(null);

        $result = $this->subject->execute($this->observerMock);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);

    }

    public function testExecute()
    {
        $orderMock = $this->createMock(\Magento\Sales\Model\Order::class);
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('__call')
            ->withConsecutive(['getOrder'], ['getQuote'])
            ->willReturn($orderMock, $quoteMock);

        $quoteMock->expects($this->once())
            ->method('getData')
            ->with('dpd_delivery_options')
            ->willReturn(['delivery_time' => 1]);

        $orderMock->expects($this->once())
            ->method('setData')
            ->with('dpd_delivery_options', ['delivery_time' => 1]);

        $this->subject->execute($this->observerMock);
    }
}
