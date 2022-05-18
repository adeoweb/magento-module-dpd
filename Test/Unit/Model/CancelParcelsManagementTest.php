<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Model\CancelParcelsManagement;
use PHPUnit\Framework\MockObject\MockObject;

class CancelParcelsManagementTest extends AbstractTest
{
    /**
     * @var CancelParcelsManagement
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $parcelDeleteRequestFactoryMock;

    /**
     * @var MockObject
     */
    private $carrierServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->parcelDeleteRequestFactoryMock = $this->createMock(
            \AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelDeleteRequestFactory::class
        );
        $this->carrierServiceMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\ServiceInterface::class);

        $this->subject = $this->objectManager->getObject(CancelParcelsManagement::class, [
            'parcelDeleteRequestFactory' => $this->parcelDeleteRequestFactoryMock,
            'carrierService' => $this->carrierServiceMock
        ]);
    }

    public function testCancelParcelsWithServiceError()
    {
        $shipmentMock = $this->createMock(\Magento\Sales\Api\Data\ShipmentInterface::class);

        $trackMock = $this->createMock(\Magento\Sales\Api\Data\ShipmentTrackInterface::class);

        $shipmentMock->expects($this->once())
            ->method('getTracks')
            ->willReturn([$trackMock]);

        $trackMock->expects($this->once())
            ->method('getTrackNumber')
            ->willReturn('LZ5158415');

        $parcelDeleteRequestMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelDeleteRequest::class);

        $this->parcelDeleteRequestFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($parcelDeleteRequestMock);

        $responseMock = $this->createMock(ResponseInterface::class);

        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->with($parcelDeleteRequestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('hasError')
            ->willReturn(true);
        $responseMock->expects($this->once())
            ->method('getErrorMessage')
            ->willReturn('Invalid response data');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid response data');

        return $this->subject->cancelParcels($shipmentMock);
    }

    public function testCancelParcels()
    {
        $shipmentMock = $this->createMock(\Magento\Sales\Api\Data\ShipmentInterface::class);

        $trackMock = $this->createMock(\Magento\Sales\Api\Data\ShipmentTrackInterface::class);

        $shipmentMock->expects($this->once())
            ->method('getTracks')
            ->willReturn([$trackMock]);

        $trackMock->expects($this->once())
            ->method('getTrackNumber')
            ->willReturn('LZ5158415');

        $parcelDeleteRequestMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelDeleteRequest::class);

        $this->parcelDeleteRequestFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($parcelDeleteRequestMock);

        $responseMock = $this->createMock(ResponseInterface::class);

        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->with($parcelDeleteRequestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('hasError')
            ->willReturn(false);

        $this->assertTrue($this->subject->cancelParcels($shipmentMock));
    }
}
