<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Location\Edit;

use AdeoWeb\Dpd\Block\Adminhtml\Location\Edit\DeleteButton;
use PHPUnit\Framework\MockObject\MockObject;

class DeleteButtonTest extends \AdeoWeb\Dpd\Test\Unit\AbstractTest
{
    /**
     * @var DeleteButton
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $urlBuilderMock;

    /**
     * @var MockObject
     */
    private $requestMock;

    public function setUp()
    {
        parent::setUp();

        $this->urlBuilderMock = $this->createMock(\Magento\Framework\Url::class);
        $this->requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);

        $contextMock = $this->createMock(\Magento\Backend\Block\Widget\Context::class);
        $contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);
        $contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->subject = $this->objectManager->getObject(DeleteButton::class, [
            'context' => $contextMock
        ]);
    }

    public function testGetButtonData()
    {
        $this->urlBuilderMock->expects($this->atleastOnce())
            ->method('getUrl')
            ->with('*/*/delete', ['location_id' => 1])
            ->willReturn('http://testcase.com/');

        $this->requestMock->expects($this->atleastOnce())
            ->method('getParam')
            ->with('location_id')
            ->willReturn(1);

        $result = $this->subject->getButtonData();
        $expectedResult = [
            'label' => __('Delete Location'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __(
                'Are you sure you want to do this?'
            ) . '\', \'http://testcase.com/\')',
            'sort_order' => 20,
        ];

        $this->assertEquals($result, $expectedResult);
    }
}
