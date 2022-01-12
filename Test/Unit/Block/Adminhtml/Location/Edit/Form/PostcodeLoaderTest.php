<?php

namespace AdeoWeb\Dpd\Test\Unit\Block\Adminhtml\Location\Edit\Form;

use AdeoWeb\Dpd\Block\Adminhtml\Location\Edit\Form\PostcodeLoader;
use AdeoWeb\Dpd\Test\Unit\AbstractTest;
use Magento\Directory\Model\Country\Postcode\ConfigInterface;

class PostcodeLoaderTest extends AbstractTest
{
    /**
     * @var PostcodeLoader
     */
    private $subject;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $postcodeConfigMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->postcodeConfigMock = $this->createMock(ConfigInterface::class);

        $this->subject = $this->objectManager->getObject(PostcodeLoader::class, [
            'postcodeConfig' => $this->postcodeConfigMock
        ]);
    }

    public function testGetPostcodesConfigJsonWithEmptyPostcodes()
    {
        $this->postcodeConfigMock->method('getPostcodes')->willReturn([]);

        $this->assertEquals('{}', $this->subject->getPostcodesConfigJson());
    }

    public function testGetPostcodesConfigJson()
    {
        $this->postcodeConfigMock->method('getPostcodes')->willReturn(['a' => 'b']);

        $this->assertEquals('{"a":"b"}', $this->subject->getPostcodesConfigJson());
    }
}
