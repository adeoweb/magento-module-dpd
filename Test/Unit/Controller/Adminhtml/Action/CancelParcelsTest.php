<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Controller\Adminhtml\Action\CancelParcels;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

class CancelParcelsTest extends AbstractTest
{
    /**
     * @var CancelParcels
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $requestMock;

    /**
     * @var MockObject
     */
    private $responseMock;

    /**
     * @var MockObject
     */
    private $messageManager;

    /**
     * @var MockObject
     */
    private $shipmentRepositoryMock;


    public function setUp(): void
    {
        parent::setUp();

        $this->requestMock = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $this->responseMock = $this->createMock(\Magento\Framework\App\Response\Http::class);
        $this->messageManager = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);

        $contextMock = $this->objectManager->getObject(\Magento\Backend\App\Action\Context::class, [
            'request' => $this->requestMock,
            'response' => $this->responseMock,
            'messageManager' => $this->messageManager
        ]);

        $this->shipmentRepositoryMock = $this->createMock(ShipmentRepositoryInterface::class);

        $this->subject = $this->objectManager->getObject(CancelParcels::class, [
            'context' => $contextMock,
            'shipmentRepository' => $this->shipmentRepositoryMock
        ]);
    }

    public function testExecuteWithoutShipmentId()
    {
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Shipment ID is not specified'));

        $this->subject->execute();
    }

    public function testExecuteWithInvalidShipment()
    {
        $this->requestMock->expects($this->atleastOnce())
            ->method('getParam')
            ->with('shipment_id')
            ->willReturn(1);

        $this->shipmentRepositoryMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(1)
            ->willThrowException(new NotFoundException(__('Entity not found')));

        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with('Entity not found');

        $this->subject->execute();
    }

    public function testExecute()
    {
        $this->requestMock->expects($this->atleastOnce())
            ->method('getParam')
            ->with('shipment_id')
            ->willReturn(1);

        $shipmentMock = $this->createMock(ShipmentInterface::class);

        $this->shipmentRepositoryMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(1)
            ->willReturn($shipmentMock);

        $this->messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with('DPD Parcels were successfully canceled');

        $this->subject->execute();
    }
}
