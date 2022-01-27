<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Order\Create\Shipping\Method\ShippingAdditional;

use AdeoWeb\Dpd\Block\Adminhtml\Order\Create\Shipping\Method\ShippingAdditional\PickupPoint as SubjectPickupPoint;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use PHPUnit\Framework\MockObject\MockObject;

class PickupPointTest extends AbstractTest
{
    /**
     * @var SubjectPickupPoint
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $quoteMock;

    /**
     * @var MockObject
     */
    private $pickupPointManagementMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $this->pickupPointManagementMock = $this->createMock(\AdeoWeb\Dpd\Model\PickupPointManagement::class);

        $quoteSessionMock = $this->createMock(\Magento\Backend\Model\Session\Quote::class);
        $quoteSessionMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($this->quoteMock);

        $this->subject = $this->objectManager->getObject(SubjectPickupPoint::class, [
            'quoteSession' => $quoteSessionMock,
            'pickupPointManagement' => $this->pickupPointManagementMock
        ]);
    }

    public function testToHtmlWithNonApplicableShippingMethod()
    {
        $shippingAddressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);
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

    public function testToHtml()
    {
        $shippingAddressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);
        $shippingAddressMock->expects($this->atleastOnce())
            ->method('getShippingMethod')
            ->willReturn('dpd_pickup');

        $this->quoteMock->expects($this->atleastOnce())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $this->subject->setTemplate('test.phtml');

        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to a member function getRelativePath() on null');

        return $this->subject->_toHtml();
    }

    public function testGetOptions()
    {
        $shippingAddressMock = $this->createMock(\Magento\Quote\Model\Quote\Address::class);
        $shippingAddressMock->expects($this->atleastOnce())
            ->method('getCountryId')
            ->willReturn('US');

        $this->quoteMock->expects($this->atleastOnce())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $this->pickupPointManagementMock->expects($this->atleastOnce())
            ->method('getList')
            ->with('US')
            ->willReturn([
                ['company' => 'SampleCompany1', 'api_id' => 1],
                ['company' => 'SampleCompany2', 'api_id' => 2],
            ]);

        $result = $this->subject->getOptions();
        $expectedResult = [
            0 =>
                [
                    'label' => 'SampleCompany1',
                    'value' => 1,
                ],
            1 =>
                [
                    'label' => 'SampleCompany2',
                    'value' => 2,
                ],
        ];

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetInputLabel()
    {
        $result = $this->subject->getInputLabel();
        $expectedResult = __('Select DPD Pickup Point:');

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetInputName()
    {
        $result = $this->subject->getInputName();
        $expectedResult = 'dpd_delivery_options[api_id]';

        $this->assertEquals($result, $expectedResult);
    }

    public function testGetInputId()
    {
        $result = $this->subject->getInputId();
        $expectedResult = 'dpd_api_id';

        $this->assertEquals($result, $expectedResult);
    }
}
