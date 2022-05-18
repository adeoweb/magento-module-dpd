<?php

namespace AdeoWeb\Dpd\Test\Unit\Controller\Adminhtml\Order;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use AdeoWeb\Dpd\Controller\Adminhtml\Order\MassPrintDpdLabels;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Exception;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Shipping\Model\Order\Track;
use Magento\Ui\Component\MassAction\Filter;
use PHPUnit\Framework\MockObject\MockObject;

class MassPrintLabelsTest extends AbstractTest
{
    /**
     * @var MassPrintDpdLabels
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $orderMock;

    /**
     * @var MockObject
     */
    private $filterMock;

    /**
     * @var MockObject
     */
    private $orderCollectionMock;

    /**
     * @var MockObject
     */
    private $shipmentMock;

    /**
     * @var MockObject
     */
    private $shipmentCollectionMock;

    /**
     * @var MockObject
     */
    private $printLabelManagementMock;

    /**
     * @var MockObject
     */
    private $messageManagerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->orderMock = $this->createMock(Order::class);
        $this->shipmentMock = $this->createMock(Order\Shipment::class);
        $this->filterMock = $this->createMock(Filter::class);
        $this->printLabelManagementMock = $this->createMock(PrintLabelManagementInterface::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);

        $this->orderCollectionMock = $this->objectManager->getCollectionMock(Collection::class, [
            $this->orderMock
        ]);

        $this->shipmentCollectionMock = $this->objectManager->getCollectionMock(
            \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection::class,
            [$this->shipmentMock]
        );

        $orderCollectionFactoryMock = $this->createConfiguredMock(
            CollectionFactory::class,
            [
                'create' => $this->orderCollectionMock,
            ]
        );

        $shipmentCollectionFactoryMock = $this->createConfiguredMock(
            \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory::class,
            ['create' => $this->shipmentCollectionMock]
        );

        $resultRedirect = $this->createMock(\Magento\Framework\Controller\Result\Redirect::class);

        $resultRedirectFactoryMock = $this->createConfiguredMock(RedirectFactory::class, [
            'create' => $resultRedirect
        ]);

        $resultFactoryMock = $this->createConfiguredMock(ResultFactory::class, [
            'create' => $resultRedirect
        ]);

        $contextMock = $this->createConfiguredMock(\Magento\Backend\App\Action\Context::class, [
            'getMessageManager' => $this->messageManagerMock,
            'getResultRedirectFactory' => $resultRedirectFactoryMock,
            'getResultFactory' => $resultFactoryMock
        ]);

        $this->subject = $this->objectManager->getObject(MassPrintDpdLabels::class, [
            'orderCollectionFactory' => $orderCollectionFactoryMock,
            'filter' => $this->filterMock,
            'shipmentCollectionFactory' => $shipmentCollectionFactoryMock,
            'printLabelManagement' => $this->printLabelManagementMock,
            'context' => $contextMock
        ]);
    }

    public function testExecuteWithException()
    {
        $this->orderCollectionMock->method('addAttributeToFilter')
            ->willReturn($this->orderCollectionMock);

        $this->filterMock->method('getCollection')
            ->willReturn($this->orderCollectionMock);

        $this->shipmentCollectionMock->method('setOrderFilter')
            ->willReturn($this->shipmentCollectionMock);
        $this->shipmentCollectionMock->method('getSize')
            ->willReturn(1);

        $shipmentTrackMock = $this->createConfiguredMock(Track::class, [
            'getTrackNumber' => '1354584'
        ]);

        $this->shipmentMock->method('getTracks')->willReturn([$shipmentTrackMock]);

        $this->filterMock->method('getCollection')->willThrowException(new Exception(__('Invalid response')));

        $this->printLabelManagementMock->method('printLabels')->willThrowException(new Exception('Invalid response'));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with('Invalid response');

        $this->subject->execute();
    }

    public function testExecute()
    {
        $this->orderCollectionMock->method('addAttributeToFilter')
            ->willReturn($this->orderCollectionMock);

        $this->filterMock->method('getCollection')
            ->willReturn($this->orderCollectionMock);

        $this->shipmentCollectionMock->method('setOrderFilter')
            ->willReturn($this->shipmentCollectionMock);
        $this->shipmentCollectionMock->method('getSize')
            ->willReturn(1);

        $shipmentTrackMock = $this->createConfiguredMock(Track::class, [
            'getTrackNumber' => '1354584'
        ]);

        $this->shipmentMock->method('getTracks')->willReturn([$shipmentTrackMock]);

        $this->subject->execute();

        $this->assertTrue(true);
    }
}
