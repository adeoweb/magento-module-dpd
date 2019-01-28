<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Order\Create\Shipping\Method\ShippingAdditional;

use AdeoWeb\Dpd\Block\Adminhtml\Order\Create\Shipping\Method\ShippingAdditional\DeliveryTime as SubjectDeliveryTime;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class DeliveryTimeTest extends AbstractTest
{
    /**
     * @var SubjectDeliveryTime
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $quoteMock;

    public function setUp()
    {
        parent::setUp();

        $this->quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);

        $quoteSessionMock = $this->createMock(\Magento\Backend\Model\Session\Quote::class);
        $quoteSessionMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($this->quoteMock);

        $carrierConfigMock = $this->objectManager->getObject(\AdeoWeb\Dpd\Helper\Config::class);

        $this->subject = $this->objectManager->getObject(SubjectDeliveryTime::class, [
            'quoteSession' => $quoteSessionMock,
            'carrierConfig' => $carrierConfigMock
        ]);
    }

    public function test_toHtmlWithNonApplicableShippingMethod()
    {
        $shippingAddressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);
        $shippingAddressMock->expects($this->atleastOnce())
            ->method('getShippingMethod')
            ->willReturn('dpd_pickup');

        $this->quoteMock->expects($this->atleastOnce())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $result = $this->subject->_toHtml();
        $expectedResult = '';

        $this->assertEquals($result, $expectedResult);
    }

    public function test_toHtmlWithApplicableShippingMethodAndIncorrectCity()
    {
        $shippingAddressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);
        $shippingAddressMock->expects($this->atleastOnce())
            ->method('getCity')
            ->willReturn('SampleCity');
        $shippingAddressMock->expects($this->atleastOnce())
            ->method('getShippingMethod')
            ->willReturn('dpd_classic');

        $this->quoteMock->expects($this->atleastOnce())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $result = $this->subject->_toHtml();
        $expectedResult = '';

        $this->assertEquals($result, $expectedResult);
    }

    public function test_toHtml()
    {
        $shippingAddressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);
        $shippingAddressMock->expects($this->atleastOnce())
            ->method('getCity')
            ->willReturn('Kaunas');
        $shippingAddressMock->expects($this->atleastOnce())
            ->method('getShippingMethod')
            ->willReturn('dpd_classic');

        $this->quoteMock->expects($this->atleastOnce())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $this->subject->setTemplate('test.phtml');

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to a member function getRelativePath() on null');

        return $this->subject->_toHtml();
    }

    public function testGetInputLabel()
    {
        $result = $this->subject->getInputLabel();
        $expectedResult = __('Select DPD Delivery Time:');

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetInputName()
    {
        $result = $this->subject->getInputName();
        $expectedResult = 'dpd_delivery_options[delivery_time]';

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetInputId()
    {
        $result = $this->subject->getInputId();
        $expectedResult = 'dpd_delivery_time';

        $this->assertEquals($result, $expectedResult);
    }
}
