<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Block\Checkout;

use AdeoWeb\Dpd\Block\Checkout\Scripts;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;

class ScriptsTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * @var Scripts
     */
    private $subject;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->any())->method('getScopeConfig')->willReturn($this->scopeConfigMock);

        $this->subject = $objectManager->getObject(Scripts::class, ['context' => $contextMock]);
    }

    /**
     * @test
     */
    public function testIsPickupPointGoogleMapsEnabled()
    {
        $this->scopeConfigMock->expects($this->any())->method('isSetFlag')->will($this->returnValueMap([
            [
                Scripts::XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_ENABLED,
                ScopeInterface::SCOPE_WEBSITES,
                null,
                true
            ]
        ]));

        $this->scopeConfigMock->expects($this->any())->method('getValue')->will($this->returnValueMap([
            [
                Scripts::XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_API_KEY,
                ScopeInterface::SCOPE_WEBSITES,
                null,
                'api_key'
            ]
        ]));

        $this->assertTrue($this->subject->isPickupPointGoogleMapsEnabled());
    }

    /**
     * @test
     */
    public function testIsPickupPointGoogleMapsEnabledConfigDisabled()
    {
        $this->scopeConfigMock->expects($this->any())->method('isSetFlag')->will($this->returnValueMap([
            [
                Scripts::XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_API_KEY,
                ScopeInterface::SCOPE_WEBSITES,
                null,
                false
            ]
        ]));

        $this->assertFalse($this->subject->isPickupPointGoogleMapsEnabled());
    }

    /**
     * @test
     */
    public function testIsPickupPointGoogleMapsEnabledEmptyApiKey()
    {
        $this->scopeConfigMock->expects($this->any())->method('isSetFlag')->will($this->returnValueMap([
            [
                Scripts::XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_API_KEY,
                ScopeInterface::SCOPE_WEBSITES,
                null,
                true
            ]
        ]));

        $this->scopeConfigMock->expects($this->any())->method('getValue')->will($this->returnValueMap([
            [
                Scripts::XML_PATH_DPD_PICKUP_POINT_GOOGLE_MAPS_API_KEY,
                ScopeInterface::SCOPE_WEBSITES,
                null,
                null
            ]
        ]));

        $this->assertFalse($this->subject->isPickupPointGoogleMapsEnabled());
    }
}
