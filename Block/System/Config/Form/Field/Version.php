<?php

namespace AdeoWeb\Dpd\Block\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\PackageInfo;

/**
 * @codeCoverageIgnore
 */
class Version extends Field
{
    /**
     * @var PackageInfo
     */
    private $packageInfo;

    public function __construct(
        Context $context,
        PackageInfo $packageInfo,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->packageInfo = $packageInfo;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('text', $this->packageInfo->getVersion('AdeoWeb_Dpd'));

        return parent::_getElementHtml($element);
    }
}
