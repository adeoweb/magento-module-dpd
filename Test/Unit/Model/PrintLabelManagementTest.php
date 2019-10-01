<?php

namespace AdeoWeb\Dpd\Test\Unit\Model;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use AdeoWeb\Dpd\Model\PrintLabelManagement;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelPrintRequest;
use AdeoWeb\Dpd\Model\Service\Dpd\Request\ParcelPrintRequestFactory;
use AdeoWeb\Dpd\Model\Service\ServiceInterface;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class PrintLabelManagementTest extends AbstractTest
{
    /**
     * @var PrintLabelManagementInterface
     */
    private $subject;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $serviceMock;

    public function setUp()
    {
        parent::setUp();

        $this->serviceMock = $this->createMock(ServiceInterface::class);

        $parcelPrintRequestMock = $this->createMock(ParcelPrintRequest::class);

        $serializerMock = $this->createMock(\AdeoWeb\Dpd\Helper\Config\Serializer::class);
        $serializerMock->expects($this->any())
            ->method('isJson')
            ->will($this->returnValueMap([
                ['{"errlog": "Invalid request"}', true]
            ]));

        $parcelPrintRequestFactoryMock = $this->createConfiguredMock(ParcelPrintRequestFactory::class, [
            'create' => $parcelPrintRequestMock
        ]);

        $this->subject = $this->objectManager->getObject(PrintLabelManagement::class, [
            'parcelPrintRequestFactory' => $parcelPrintRequestFactoryMock,
            'serializer' => $serializerMock,
            'dpdService' => $this->serviceMock
        ]);
    }

    public function testPrintLabelsWithEmptyLabelNumbersArray()
    {
        $this->assertNull($this->subject->printLabels([]));
        $this->assertNull($this->subject->printLabels(null));
        $this->assertNull($this->subject->printLabels(''));
    }

    public function testPrintLabelsWithErrorFromApi()
    {
        $this->serviceMock->method('call')
            ->willReturn('{"errlog": "Invalid request"}');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error: Invalid request');

        $this->subject->printLabels(['test123']);
    }

    public function testPrintLabels()
    {
        $this->serviceMock->method('call')
            ->willReturn('%PDF-testpdfcontent');

        $result = $this->subject->printLabels(['test123']);

        $this->assertEquals('%PDF-testpdfcontent', $result);
    }
}
