<?php

namespace AdeoWeb\Dpd\Test\Model;

use AdeoWeb\Dpd\Model\CloseManifestManagement;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelManifestPrintRequest;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelManifestPrintRequestFactory;
use AdeoWeb\Dpd\Model\Service\ResponseInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
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

    public function setUp()
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
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->carrierServiceMock->expects($this->once())
            ->method('call')
            ->with($this->parcelManifestPrintRequestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('hasError')
            ->willReturn(true);
        $responseMock->expects($this->once())
            ->method('getErrorMessage')
            ->willReturn('Invalid response data');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid response data');

        return $this->subject->closeManifest();
    }

    public function testCloseManifest()
    {
        $responseMock = $this->createMock(ResponseInterface::class);

        $this->carrierServiceMock->expects($this->atLeastOnce())
            ->method('call')
            ->with($this->parcelManifestPrintRequestMock)
            ->willReturn($responseMock);

        $responseMock->expects($this->atLeastOnce())
            ->method('hasError')
            ->willReturn(false);
        $responseMock->expects($this->atLeastOnce())
            ->method('getBody')
            ->with('pdf')
            ->willReturn('pdfcontent');

        $result = $this->subject->closeManifest();
        $expectedValue = ['pdfcontent','pdfcontent','pdfcontent', 'pdfcontent'];

        $this->assertEquals($expectedValue, $result);
    }
}
