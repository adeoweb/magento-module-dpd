<?php

namespace AdeoWeb\Dpd\Test\Unit\Plugin\Block\Adminhtml\Order;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Plugin\Block\Adminhtml\Order\View;

class ViewTest extends AbstractTest
{
    /**
     * @var View
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(View::class);
    }

    public function testButtonAddedForDpdCarrier()
    {
        $subjectMock = $this->createMock(\Magento\Sales\Block\Adminhtml\Order\View::class);
        $layoutMock = $this->createMock(\Magento\Framework\View\LayoutInterface::class);

        $orderMock = $this->createMock(\Magento\Sales\Model\Order::class);
        $subjectMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $shippingMethodMock = $this->createMock(\Magento\Framework\DataObject::class);

        $orderMock->expects($this->atLeastOnce())
            ->method('getShippingMethod')
            ->with(true)
            ->willReturn($shippingMethodMock);

        $shippingMethodMock->expects($this->once())
            ->method('getData')
            ->with('carrier_code')
            ->willReturn('dpd');

        $subjectMock->expects($this->once())
            ->method('addButton')
            ->with('dpd_collection_request', $this->anything());

        $this->subject->beforeSetLayout($subjectMock, $layoutMock);
    }
}
