<?php

namespace AdeoWeb\Dpd\Test\Model;

use AdeoWeb\Dpd\Model\CloseManifestManagement;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelManifestPrintRequest;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelManifestPrintRequestFactory;
use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;

class CloseManifestManagementTest extends AbstractTest
{
    /**
     * @var CloseManifestManagement
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $parcelManifestPrintRequestMock;

    /**
     * @var MockObject
     */
    private $carrierServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->parcelManifestPrintRequestMock = $this->createMock(ParcelManifestPrintRequest::class);
        $this->carrierServiceMock = $this->createMock(\AdeoWeb\Dpd\Model\Service\ServiceInterface::class);

        $parcelManifestPrintRequestFactoryMock = $this->createMock(ParcelManifestPrintRequestFactory::class);
        $parcelManifestPrintRequestFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->parcelManifestPrintRequestMock);

        $this->subject = $this->objectManager->getObject(CloseManifestManagement::class, [
            'parcelManifestPrintRequestFactory' => $parcelManifestPrintRequestFactoryMock,
            'carrierService' => $this->carrierServiceMock
        ]);
    }

    public function testCloseManifestWithException()
    {
        $this->carrierServiceMock->expects($this->atleastOnce())
            ->method('call')
            ->with($this->parcelManifestPrintRequestMock)
            ->willReturn('{"err":"true"}');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('No new shipments were created.');

        return $this->subject->closeManifest();
    }

    public function testCloseManifest()
    {
        $this->carrierServiceMock->expects($this->atLeastOnce())
            ->method('call')
            ->with($this->parcelManifestPrintRequestMock)
            ->willReturn('testPDFString');

        $result = $this->subject->closeManifest();
        $expectedValue = ['testPDFString','testPDFString','testPDFString', 'testPDFString'];

        $this->assertEquals($expectedValue, $result);
    }
}
