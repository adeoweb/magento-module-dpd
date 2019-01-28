<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Location\Edit;

use AdeoWeb\Dpd\Block\Adminhtml\Location\Edit\SaveAndContinueButton;
use PHPUnit\Framework\MockObject\MockObject;

class SaveAndContinueButtonTest extends \AdeoWeb\Dpd\Test\Unit\AbstractTest
{
    /**
     * @var SaveAndContinueButton
     */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = $this->objectManager->getObject(SaveAndContinueButton::class);
    }

    public function testGetButtonData()
    {
        $result = $this->subject->getButtonData();
        $expectedResult = [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 80,
        ];

        $this->assertEquals($result, $expectedResult);
    }
}
