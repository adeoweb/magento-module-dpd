<?php

namespace AdeoWeb\Dpd\Test\Unit;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }
}
