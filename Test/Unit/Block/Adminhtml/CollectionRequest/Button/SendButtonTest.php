<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\CollectionRequest\Button;

use AdeoWeb\Dpd\Block\Adminhtml\CollectionRequest\Button\SendButton;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class SendButtonTest extends TestCase
{
    /**
     * @var SendButton
     */
    private $subject;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->subject = $objectManager->getObject(SendButton::class);
    }

    /**
     * @test
     */
    public function testGetButtonData()
    {
        $this->assertArraySubset([
            'label' => 'Send',
            'data_attribute' => ['mage-init' => ['button' => ['event' => 'save']]]
        ], $this->subject->getButtonData());
    }
}
