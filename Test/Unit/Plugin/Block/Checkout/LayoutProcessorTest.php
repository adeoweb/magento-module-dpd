<?php

namespace AdeoWeb\Dpd\Test\Unit\Plugin\Block\Checkout;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Plugin\Block\Checkout\LayoutProcessor;
use PHPUnit\Framework\MockObject\MockObject;

class LayoutProcessorTest extends AbstractTest
{
    /**
     * @var LayoutProcessor
     */
    private $subject;

    /**
     * @var MockObject
     */
    private $subjectMock;

    /**
     * @var MockObject
     */
    private $scopeConfigMock;

    public function setUp()
    {
        parent::setUp();

        $this->subjectMock = $this->createMock(\Magento\Checkout\Block\Checkout\LayoutProcessor::class);
        $this->scopeConfigMock = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $carrierConfig = $this->objectManager->getObject(\AdeoWeb\Dpd\Helper\Config::class);

        $this->subject = $this->objectManager->getObject(LayoutProcessor::class, [
            'scopeConfig' => $this->scopeConfigMock,
            'carrierConfig' => $carrierConfig
        ]);
    }

    public function testAppendDpdMethodComponentsWithNonExistantComponent()
    {
        $result = $this->subject->afterProcess($this->subjectMock, []);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testAppendDpdMethodComponentsWithInvalidComponent()
    {
        $jsLayoutResult = [];
        $jsLayoutResult['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = [];

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->with('carriers/dpd/allowed_methods')
            ->willReturn('test_method');

        $result = $this->subject->afterProcess($this->subjectMock, $jsLayoutResult);
        $expectedResult = $jsLayoutResult;

        $this->assertEquals($expectedResult, $result);
    }

    public function testAppendDpdMethodClassicMethodWithDisabledDeliveryTimeComponent()
    {
        $jsLayoutResult = [];
        $jsLayoutResult['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = [];

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->with('carriers/dpd/allowed_methods')
            ->willReturn('classic');
        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/classic/delivery_times_enable')
            ->willReturn(false);

        $result = $this->subject->afterProcess($this->subjectMock, $jsLayoutResult);
        $expectedResult = $jsLayoutResult;

        $this->assertEquals($expectedResult, $result);
    }

    public function testAppendDpdMethodComponents()
    {
        $jsLayoutResult = [];
        $jsLayoutResult['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = [];

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->with('carriers/dpd/allowed_methods')
            ->willReturn('classic,pickup');
        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('isSetFlag')
            ->with('carriers/dpd/classic/delivery_times_enable')
            ->willReturn(true);

        $result = $this->subject->afterProcess($this->subjectMock, $jsLayoutResult);
        $expectedResult = $jsLayoutResult;
        $expectedResult['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shippingAdditional']['children'] = array (
            'dpd_method_classic_component_delivery-time' =>
                array (
                    'component' => 'AdeoWeb_Dpd/js/view/checkout/shipping/delivery-time',
                ),
            'dpd_method_pickup_component_0' =>
                array (
                    'component' => 'AdeoWeb_Dpd/js/view/checkout/shipping/pickup-point',
                ),
        );

        $this->assertEquals($expectedResult, $result);
    }
}
