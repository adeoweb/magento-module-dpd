<?php

namespace AdeoWeb\Dpd\Test\Unit\Model\Config\Source;

use AdeoWeb\Dpd\Model\Config\Source\PageFormat;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;

class PageFormatTest extends AbstractTest
{
    /**
     * @var PageFormat
     */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $carrierConfig = $this->objectManager->getObject(\AdeoWeb\Dpd\Helper\Config::class);

        $this->subject = $this->objectManager->getObject(PageFormat::class, [
            'carrierConfig' => $carrierConfig,
        ]);
    }

    public function testToOptionArray()
    {
        $result = $this->subject->toOptionArray();

        $expectedResult = [
            0 =>
                [
                    'value' => 'A4',
                    'label' => 'A4',
                ],
            1 =>
                [
                    'value' => 'A6',
                    'label' => 'A6',
                ],
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
