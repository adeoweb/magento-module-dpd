<?php

namespace AdeoWeb\Dpd\Test\Unit\Plugin\Model\AdminOrder;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Plugin\Model\AdminOrder\Create;
use Magento\Framework\App\Request\Http;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;

class CreateTest extends AbstractTest
{
    /**
     * @var Create
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $requestMock;

    public function setUp()
    {
        parent::setUp();

        $this->requestMock = $this->createMock(Http::class);

        $this->subject = $this->objectManager->getObject(Create::class, [
            'request' => $this->requestMock
        ]);
    }

    public function testBeforeCreateOrderWithoutDeliveryOptions()
    {
        $subjectMock = $this->createMock(\Magento\Sales\Model\AdminOrder\Create::class);

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('dpd_delivery_options')
            ->willReturn([]);

        $result = $this->subject->beforeCreateOrder($subjectMock);

        $this->assertNull($result);
    }

    public function testBeforeCreateOrder()
    {
        $subjectMock = $this->createMock(\Magento\Sales\Model\AdminOrder\Create::class);

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->with('dpd_delivery_options')
            ->willReturn(['a' => 'b']);

        $quoteSubstitute = $this->objectManager->getObject(DataObject::class);

        $subjectMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteSubstitute);

        $this->subject->beforeCreateOrder($subjectMock);

        $this->assertEquals($quoteSubstitute->getData('dpd_delivery_options'), '{"a":"b"}');
    }
}
