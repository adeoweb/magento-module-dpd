<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Form\Field;

use Magento\Directory\Model\Config\Source\CountryFactory;
use Magento\Framework\View\Element\Context;

class Country extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var CountryFactory
     */
    private $countrySourceFactory;

    public function __construct(
        Context $context,
        CountryFactory $countrySourceFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->countrySourceFactory = $countrySourceFactory;
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            /** @var \Magento\Directory\Model\Config\Source\Country $countrySource */
            $countrySource = $this->countrySourceFactory->create();

            foreach ($countrySource->toOptionArray() as $country) {
                $this->addOption($country['value'], $country['label']);
            }
        }
        return parent::_toHtml();
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setData('name', $value);
    }
}