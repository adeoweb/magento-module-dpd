<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Location\Edit\Form;

use Magento\Backend\Block\Template;
use Magento\Directory\Model\Country\Postcode\ConfigInterface;

class PostcodeLoader extends Template
{
    /**
     * @var ConfigInterface
     */
    private $postcodeConfig;

    public function __construct(
        Template\Context $context,
        ConfigInterface $postcodeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->postcodeConfig = $postcodeConfig;
    }

    /**
     * @return false|string
     */
    public function getPostcodesConfigJson()
    {
        $postcodes = $this->postcodeConfig->getPostCodes();

        if (!$postcodes) {
            return '{}';
        }

        return \json_encode($postcodes);
    }
}