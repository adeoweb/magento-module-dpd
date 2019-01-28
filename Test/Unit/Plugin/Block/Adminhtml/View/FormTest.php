<?php

namespace AdeoWeb\Dpd\Test\Unit\Plugin\Block\Adminhtml\View;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Plugin\Block\Adminhtml\View\Form;
use Magento\Framework\View\LayoutInterface;

class FormTest extends AbstractTest
{
    /**
     * @var Form
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(Form::class);
    }

    public function testCancelDpdParcelsButtonIsAdded()
    {
        $subjectMock = $this->createMock(\Magento\Shipping\Block\Adminhtml\View\Form::class);
        $methodResult = '';

        $shipmentMock = $this->createMock(\Magento\Sales\Model\Order\Shipment::class);
        $layoutMock = $this->createMock(LayoutInterface::class);

        $subjectMock->expects($this->atLeastOnce())
            ->method('getShipment')
            ->willReturn($shipmentMock);
        $subjectMock->expects($this->atLeastOnce())
            ->method('getUrl')
            ->with('dpd/action/cancelParcels', $this->anything())
            ->willReturn('https://testurl.com');
        $subjectMock->expects($this->atLeastOnce())
            ->method('getLayout')
            ->willReturn($layoutMock);

        $buttonBlock = $this->createMock(\Magento\Backend\Block\Widget\Button::class);

        $layoutMock->expects($this->atLeastOnce())
            ->method('createBlock')
            ->with(\Magento\Backend\Block\Widget\Button::class)
            ->willReturn($buttonBlock);

        $buttonBlock->expects($this->once())
            ->method('setData')
            ->with(['label' => __('Cancel DPD Parcels'), 'onclick' => 'setLocation(\'https://testurl.com\')'])
            ->willReturn($buttonBlock);

        $this->subject->afterGetPrintLabelButton($subjectMock, $methodResult);
    }

}