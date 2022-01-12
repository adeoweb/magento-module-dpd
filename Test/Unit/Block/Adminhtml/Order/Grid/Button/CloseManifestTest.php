<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Order\Grid\Button;

use AdeoWeb\Dpd\Block\Adminhtml\Order\Grid\Button\CloseManifest;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class CloseManifestTest extends AbstractTest
{
    /**
     * @var CloseManifest
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $urlBuilderMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->urlBuilderMock = $this->createMock(\Magento\Framework\Url::class);

        $contextMock = $this->createMock(\Magento\Backend\Block\Widget\Context::class);
        $contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->subject = $this->objectManager->getObject(CloseManifest::class, [
            'context' => $contextMock
        ]);
    }

    public function testGetButtonData()
    {
        $this->urlBuilderMock->expects($this->atleastOnce())
            ->method('getUrl')
            ->with('dpd/action/closeManifest')
            ->willReturn('http://testcase.com');

        $result = $this->subject->getButtonData();
        $expectedResult = [
            'label' => __('Close DPD Manifest'),
            'on_click' => "confirmSetLocation('Are you sure you want to close DPD manifest?', 'http://testcase.com')",
            'class' => 'action-secondary'
        ];

        $this->assertEquals($result, $expectedResult);
    }
}
