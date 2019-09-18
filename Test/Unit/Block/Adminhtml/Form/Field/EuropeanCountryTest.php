<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Form\Field;

use AdeoWeb\Dpd\Block\Adminhtml\Form\Field\Country;
use AdeoWeb\Dpd\Block\Adminhtml\Form\Field\EuropeanCountry;
use Magento\Directory\Model\Config\Source\CountryFactory;
use PHPUnit\Framework\MockObject\MockObject;

class EuropeanCountryTest extends \AdeoWeb\Dpd\Test\Unit\AbstractTest
{
    /**
     * @var Country
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $europeanCountriesSourceMock;

    public function setUp()
    {
        parent::setUp();

        $this->europeanCountriesSourceMock = $this->createMock(\AdeoWeb\Dpd\Model\Config\Source\EuropeanCountries::class);

        $escaperMock = $this->objectManager->getObject(\Magento\Framework\Escaper::class, [
            'escapeHtml'
        ]);

        $contextMock = $this->createMock(\Magento\Framework\View\Element\Context::class);
        $contextMock->expects($this->any())
            ->method('getEscaper')
            ->willReturn($escaperMock);

        $this->subject = $this->objectManager->getObject(EuropeanCountry::class, [
            'getData',
            'context' => $contextMock,
            'europeanCountriesSource' => $this->europeanCountriesSourceMock
        ]);
    }

    public function testToHtml()
    {
        $this->europeanCountriesSourceMock->expects($this->atleastOnce())
            ->method('toOptionArray')
            ->willReturn([
                ['value' => 'test1', 'label' => 'Sample Label 1'],
                ['value' => 'test2', 'label' => 'Sample Label 2']
            ]);

        $result = $this->subject->_toHtml();
        $expectedResult = '<select name="" id="" class="" title="" ><option value="test1" >Sample Label 1</option><option value="test2" >Sample Label 2</option></select>';

        $this->assertEquals($result, $expectedResult);
    }

    public function testSetInputName()
    {
        $this->subject->setInputName('test');

        $result = $this->subject->getData('name');
        $expectedResult = 'test';

        $this->assertEquals($result, $expectedResult);
    }
}
