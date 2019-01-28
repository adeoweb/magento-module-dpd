<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Location\Edit;

use AdeoWeb\Dpd\Block\Adminhtml\Location\Edit\BackButton;
use PHPUnit\Framework\MockObject\MockObject;

class BackButtonTest extends \AdeoWeb\Dpd\Test\Unit\AbstractTest
{
    /**
     * @var BackButton
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $urlBuilderMock;

    public function setUp()
    {
        parent::setUp();

        $this->urlBuilderMock = $this->createMock(\Magento\Framework\Url::class);

        $contextMock = $this->createMock(\Magento\Backend\Block\Widget\Context::class);
        $contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->subject = $this->objectManager->getObject(BackButton::class, [
            'context' => $contextMock
        ]);
    }

    public function testGetButtonData()
    {
        $this->urlBuilderMock->expects($this->atleastOnce())
            ->method('getUrl')
            ->with('*/*/', [])
            ->willReturn('http://testcase.com/');

        $result = $this->subject->getButtonData();
        $expectedResult = [
            'label' => __('Back'),
            'on_click' => "location.href = 'http://testcase.com/';",
            'class' => 'back',
            'sort_order' => 10
        ];

        $this->assertEquals($result, $expectedResult);
    }
}
