<?php

namespace AdeoWeb\Dpd\Test\Unit\Plugin\Controller\Adminhtml\Order\Shipment;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use AdeoWeb\Dpd\Plugin\Controller\Adminhtml\Order\Shipment\PrintLabel;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Closure;
use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Controller\Adminhtml\Order\Shipment\PrintLabel as PrintLabelObject;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;
use Magento\Shipping\Model\Order\Track;
use PHPUnit\Framework\MockObject\MockObject;

class PrintLabelTest extends AbstractTest
{
    /**
     * @var PrintLabel
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $orderMock;

    /**
     * @var MockObject
     */
    private $shipmentMock;

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

        $trackMock = $this->createConfiguredMock(Track::class, [
            'getTrackNumber' => 'abc123'
        ]);

        $this->orderMock = $this->createMock(Order::class);
        $this->shipmentMock = $this->createConfiguredMock(Order\Shipment::class, [
            'getOrder' => $this->orderMock,
            'getTracks' => [$trackMock]
        ]);
        $this->printLabelManagementMock = $this->createMock(PrintLabelManagementInterface::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);

        $shipmentLoaderMock = $this->createConfiguredMock(ShipmentLoader::class, [
            'load' => $this->shipmentMock,
        ]);

        $fileResultMock = $this->createMock(\Magento\Framework\App\ResponseInterface::class);

        $fileFactoryMock = $this->createConfiguredMock(\Magento\Framework\App\Response\Http\FileFactory::class, [
            'create' => $fileResultMock
        ]);

        $this->subject = $this->objectManager->getObject(PrintLabel::class, [
            'shipmentLoader' => $shipmentLoaderMock,
            'printLabelManagement' => $this->printLabelManagementMock,
            'messageManager' => $this->messageManagerMock,
            'fileFactory' => $fileFactoryMock
        ]);
    }

    public function testAroundExecuteWithNonDpdShippingMethod()
    {
        $this->orderMock->method('getShippingMethod')->willReturn('test_method');

        $callback = Closure::fromCallable(function () {
            return true;
        });

        $subjectMock = $this->createMock(PrintLabelObject::class);

        $result = $this->subject->aroundExecute($subjectMock, $callback);

        $this->assertTrue($result);
    }

    public function testAroundExecuteWithPrintLabelsException()
    {
        $this->orderMock->method('getShippingMethod')->willReturn('dpd_classic');

        $this->printLabelManagementMock->method('printLabels')->willThrowException(new Exception('Invalid request'));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with('Invalid request');

        $callback = Closure::fromCallable(function () {
            return true;
        });

        $subjectMock = $this->createMock(PrintLabelObject::class);

        $result = $this->subject->aroundExecute($subjectMock, $callback);

        $this->assertTrue($result);
    }

    public function testAroundExecute()
    {
        $this->orderMock->method('getShippingMethod')->willReturn('dpd_classic');

        $callback = Closure::fromCallable(function () {
            return true;
        });

        $subjectMock = $this->createMock(PrintLabelObject::class);

        $result = $this->subject->aroundExecute($subjectMock, $callback);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
