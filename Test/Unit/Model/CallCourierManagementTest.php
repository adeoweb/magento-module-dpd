<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupOrderSaveRequest;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Model\CallCourierManagement;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;

class CallCourierManagementTest extends AbstractTest
{
    /**
     * @var CallCourierManagement
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $callCourierRequestReaderMock;

    /**
     * @var MockObject
     */
    private $locationRepositoryMock;

    /**
     * @var MockObject
     */
    private $pickupOrderSaveRequestFactoryMock;

    /**
     * @var MockObject
     */
    private $carrierServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->callCourierRequestReaderMock = $this->createMock(
            \AdeoWeb\Dpd\Helper\SubjectReader\CallCourierRequest::class
        );
        $this->locationRepositoryMock = $this->createMock(LocationRepositoryInterface::class);
        $this->pickupOrderSaveRequestFactoryMock = $this->createMock(
            \AdeoWeb\Dpd\Model\Service\Dpd\Request\PickupOrderSaveRequestFactory::class
        );
        $this->carrierServiceMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\ServiceInterface::class);

        $this->subject = $this->objectManager->getObject(CallCourierManagement::class, [
            'callCourierRequestReader' => $this->callCourierRequestReaderMock,
            'locationRepository' => $this->locationRepositoryMock,
            'pickupOrderSaveRequestFactory' => $this->pickupOrderSaveRequestFactoryMock,
            'carrierService' => $this->carrierServiceMock
        ]);
    }

    public function testCallCourierWithInvalidPickupTime()
    {
        $data = ['request' => []];

        $pickupDate = (new \DateTime('+1 day'))->format('m/d/Y');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupDate')
            ->willReturn($pickupDate);

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupTime')
            ->willReturn('0000');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Invalid pickup time value');

        return $this->subject->callCourier($data);
    }

    public function testCallCourierWithEarlierPickupTime()
    {
        $data = ['request' => []];

        $pickupDate = (new \DateTime('-1 day'))->format('m/d/Y');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupDate')
            ->willReturn($pickupDate);

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupTime')
            ->willReturn('00:00');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Incorrect pickup time');

        return $this->subject->callCourier($data);
    }

    public function testCallCourierWithInvalidResponse()
    {
        $data = ['request' => []];

        $pickupDate = (new \DateTime('+1 day'))->format('m/d/Y');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupDate')
            ->willReturn($pickupDate);

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupTime')
            ->willReturn('00:00');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readWorkUntil')
            ->willReturn('18:00');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readWarehouseId')
            ->willReturn(1);

        $locationMock = $this->createMock(LocationInterface::class);

        $this->locationRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($locationMock);

        $pickupOrderSaveRequestMock = $this->createMock(PickupOrderSaveRequest::class);

        $this->pickupOrderSaveRequestFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($pickupOrderSaveRequestMock);

        $this->expectException(\Exception::class);

        return $this->subject->callCourier($data);
    }

    public function testCallCourier()
    {
        $data = ['request' => []];

        $pickupDate = (new \DateTime('+1 day'))->format('m/d/Y');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupDate')
            ->willReturn($pickupDate);

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readPickupTime')
            ->willReturn('00:00');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readWorkUntil')
            ->willReturn('18:00');

        $this->callCourierRequestReaderMock->expects($this->atLeastOnce())
            ->method('readWarehouseId')
            ->willReturn(1);

        $locationMock = $this->createMock(LocationInterface::class);

        $this->locationRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($locationMock);

        $pickupOrderSaveRequestMock = $this->createMock(PickupOrderSaveRequest::class);

        $this->pickupOrderSaveRequestFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($pickupOrderSaveRequestMock);

        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->with($pickupOrderSaveRequestMock)
            ->willReturn('DONE');

        $this->assertTrue($this->subject->callCourier($data));
    }
}
