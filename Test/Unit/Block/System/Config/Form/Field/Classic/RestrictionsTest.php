<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\System\Config\Form\Field\Classic;

use AdeoWeb\Dpd\Block\System\Config\Form\Field\Classic\Restrictions;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;

class RestrictionsTest extends AbstractTest
{
    /**
     * @var Restrictions
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $layoutMock;

    public function setUp()
    {
        parent::setUp();

        $this->layoutMock = $this->createMock(\Magento\Framework\View\LayoutInterface::class);

        $contextMock = $this->createMock(\Magento\Backend\Block\Template\Context::class);
        $contextMock->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layoutMock);

        $this->subject = $this->objectManager->getObject(Restrictions::class, [
            'context' => $contextMock
        ]);
    }

    public function testRenderCellTemplate()
    {
        $element = $this->objectManager->getObject(DataObject::class);
        $element->setName('test');

        $this->subject->setElement($element);
        $this->subject->addColumn('price', []);

        $result = $this->subject->renderCellTemplate('price');
        $expectedResult = '<input type="text" id="<%- _id %>_price" name="test[<%- _id %>][price]" value="<%- price %>"  class="input-text required-entry validate-number" style="width:50px"/>';

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetArrayRows()
    {
        $element = $this->objectManager->getObject(DataObject::class);
        $element->setName('test');
        $element->setValue([
            0 => ['country' => 'US']
        ]);

        $this->subject->setElement($element);

        $countryFieldMock = $this->objectManager->getObject(\AdeoWeb\Dpd\Block\Adminhtml\Form\Field\Country::class);

        $this->layoutMock->expects($this->atLeastOnce())
            ->method('createBlock')
            ->with(
                \AdeoWeb\Dpd\Block\Adminhtml\Form\Field\Country::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            )->willReturn($countryFieldMock);

        $result = $this->subject->getArrayRows();

        $this->assertInstanceOf(DataObject::class, $result[0]);
    }

    public function testRender()
    {
        $elementMock = $this->createMock(\Magento\Framework\Data\Form\Element\AbstractElement::class);

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to a member function dispatch() on null');
        return $this->subject->render($elementMock);
    }
}