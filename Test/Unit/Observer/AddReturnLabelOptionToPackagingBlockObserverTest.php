<?php

namespace AdeoWeb\Dpd\Test\Unit\Observer;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Observer\AddReturnLabelOptionToPackagingBlockObserver;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;

class AddReturnLabelOptionToPackagingBlockObserverTest extends AbstractTest
{
    /**
     * @var AddReturnLabelOptionToPackagingBlockObserver
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

        $this->subject = $this->objectManager->getObject(AddReturnLabelOptionToPackagingBlockObserver::class);
    }

    public function testExecuteWithWrongBlockType()
    {
        $blockMock = $this->createMock(DataObject::class);

        $this->eventMock->expects($this->once())
            ->method('getData')
            ->with('block')
            ->willReturn($blockMock);

        $result = $this->subject->execute($this->observerMock);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testExecute()
    {
        $blockMock = $this->createMock(\Magento\Shipping\Block\Adminhtml\Order\Packaging::class);
        $transportMock = $this->createMock(DataObject::class);

        $this->eventMock->expects($this->atLeastOnce())
            ->method('getData')
            ->withConsecutive(['block'], ['transport'])
            ->willReturnOnConsecutiveCalls($blockMock, $transportMock);

        $transportMock->expects($this->atLeastOnce())
            ->method('__call')
            ->withConsecutive(['getHtml'], ['setHtml', ['<p><div id="packaging_window">test</div></p>']])
            ->willReturn('<p><div id="packaging_window"></div></p>', $transportMock);

        $layoutMock = $this->createMock(\Magento\Framework\View\LayoutInterface::class);
        $blockMock->expects($this->atLeastOnce())
            ->method('getLayout')
            ->willReturn($layoutMock);

        $returnLabelMock = $this->createMock(\AdeoWeb\Dpd\Block\Adminhtml\Order\Packaging\ReturnLabelField::class);
        $layoutMock->expects($this->atLeastOnce())
            ->method('createBlock')
            ->with(\AdeoWeb\Dpd\Block\Adminhtml\Order\Packaging\ReturnLabelField::class)
            ->willReturn($returnLabelMock);

        $returnLabelMock->expects($this->atLeastOnce())
            ->method('toHtml')
            ->willReturn('test');

        $this->subject->execute($this->observerMock);
    }
}