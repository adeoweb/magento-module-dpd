<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\System\Config\Form\Field;

use AdeoWeb\Dpd\Block\System\Config\Form\Field\EuropeanRestrictions;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Framework\DataObject;

class EuropeanRestrictionsTest extends AbstractTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutMock;

    /**
     * @var EuropeanRestrictions
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->layoutMock = $this->createMock(\Magento\Framework\View\LayoutInterface::class);

        $contextMock = $this->createMock(\Magento\Backend\Block\Template\Context::class);
        $contextMock->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layoutMock);

        $this->subject = $this->objectManager->getObject(EuropeanRestrictions::class, [
            'context' => $contextMock,
        ]);
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
                \AdeoWeb\Dpd\Block\Adminhtml\Form\Field\EuropeanCountry::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            )->willReturn($countryFieldMock);

        $result = $this->subject->getArrayRows();

        $this->assertInstanceOf(DataObject::class, $result[0]);
    }
}
