<?php

namespace AdeoWeb\Dpd\Test\Unit\Helper;

use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use AdeoWeb\Dpd\Helper\Config;

class ConfigTest extends AbstractTest
{
    /**
     * @var Config
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(Config::class);
    }

    public function testGetCodeWithNonExistantType()
    {
        $type = 'test123';

        $result = $this->subject->getCode($type);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetCodeWithEmptyCode()
    {
        $type = 'method';

        $result = $this->subject->getCode($type);
        $expectedResult = [
            'classic' => __('DPD - Classic'),
            'pickup' => __('DPD - Pickup'),
            'saturday' => __('DPD - Saturday'),
            'sameday' => __('DPD - Same Day')
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetCodeWithIncorrectCode()
    {
        $type = 'method';
        $code = 'test123';

        $result = $this->subject->getCode($type, $code);
        $expectedResult = null;

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetCode()
    {
        $type = 'method';
        $code = 'classic';

        $result = $this->subject->getCode($type, $code);
        $expectedResult = __('DPD - Classic');

        $this->assertEquals($expectedResult, $result);
    }
}
