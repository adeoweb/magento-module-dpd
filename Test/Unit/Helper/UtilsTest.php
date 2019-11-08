<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Test\Unit\Helper;

use AdeoWeb\Dpd\Helper\Utils;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    /**
     * @var Utils
     */
    private $subject;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->subject = $objectManager->getObject(Utils::class);
    }

    public function testFormatPostcode()
    {
        $this->assertEquals('1000', $this->subject->formatPostcode('EN-1000'));
    }
}
