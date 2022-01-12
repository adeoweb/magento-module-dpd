<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\System\Config\Form\Field;

use AdeoWeb\Dpd\Block\Adminhtml\Form\Field\Country;
use AdeoWeb\Dpd\Block\System\Config\Form\Field\Restrictions;
use AdeoWeb\Dpd\Block\System\Config\Form\Field\WeightPrice as WeightPriceBlock;
use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice;
use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPriceFactory;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;

class RestrictionsTest extends AbstractTest
{
    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var Restrictions
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $layoutMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->layoutMock = $this->createMock(LayoutInterface::class);

        $weightPriceMock = $this->createMock(WeightPrice::class);
        $weightPriceFactoryMock = $this->createMock(WeightPriceFactory::class);
        $weightPriceFactoryMock->expects($this->any())->method('create')->willReturn($weightPriceMock);

        $managerMock = $this->createMock(ManagerInterface::class);
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $elementMock = $this->createMock(Text::class);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->expects($this->any())->method('getEventManager')->willReturn($managerMock);
        $this->contextMock->expects($this->any())->method('getScopeConfig')->willReturn($scopeConfig);
        $this->contextMock->expects($this->any())->method('getLayout')->willReturn($this->layoutMock);

        $this->subject = $this->objectManager->getObject(Restrictions::class, [
            'context' => $this->contextMock,
            'weightPriceFactory' => $weightPriceFactoryMock
        ]);
        $this->subject->setData('element', $elementMock);
    }

    public function testToHtml()
    {
        $this->subject->setTemplate(null);
        $weightPriceRendererMock = $this->createMock(WeightPriceBlock::class);
        $countryRendererMock = $this->createMock(Country::class);

        $this->layoutMock->expects($this->any())
            ->method('createBlock')
            ->will($this->returnValueMap([
                [Country::class, '', ['data' => ['is_render_to_js_template' => true]], $countryRendererMock],
                [
                    WeightPriceBlock::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]],
                    $weightPriceRendererMock
                ]
            ]));

        $this->assertEquals('', $this->subject->toHtml());
    }

    public function testGetWeightPriceJs()
    {
        $weightPriceRendererMock = $this->createMock(WeightPriceBlock::class);
        $weightPriceRendererMock->expects($this->any())->method('getJsHtml')->willReturn('js html');

        $countryRendererMock = $this->createMock(Country::class);

        $this->layoutMock->expects($this->any())
            ->method('createBlock')
            ->will($this->returnValueMap([
                [Country::class, '', ['data' => ['is_render_to_js_template' => true]], $countryRendererMock],
                [
                    WeightPriceBlock::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]],
                    $weightPriceRendererMock
                ]
            ]));

        $this->assertEquals('js html', $this->subject->getWeightPriceJs());
    }

    public function testRenderCellTemplate()
    {
        $element = $this->objectManager->getObject(DataObject::class);
        $element->setName('test');

        $this->subject->setElement($element);
        $this->subject->addColumn('price', []);

        $result = $this->subject->renderCellTemplate('price');
        $expectedResult = '<input type="text" id="<%- _id %>_price" name="test[<%- _id %>][price]" value="<%- price %>"  class="input-text"/>';

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetArrayRows()
    {
        $element = $this->objectManager->getObject(DataObject::class);
        $element->setName('test');
        $element->setValue([
            0 => ['country' => 'US']
        ]);

        $this->subject->setElement($element);

        $countryFieldMock = $this->objectManager->getObject(Country::class);

        $this->layoutMock->expects($this->atLeastOnce())
            ->method('createBlock')
            ->with(
                Country::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            )->willReturn($countryFieldMock);

        $result = $this->subject->getArrayRows();

        $this->assertInstanceOf(DataObject::class, $result[0]);
    }
}
