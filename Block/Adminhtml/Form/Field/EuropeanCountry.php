<?php

namespace AdeoWeb\Dpd\Block\Adminhtml\Form\Field;

use AdeoWeb\Dpd\Model\Config\Source\EuropeanCountries;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class EuropeanCountry extends Select
{
    /**
     * @var EuropeanCountries
     */
    private $europeanCountriesSource;

    public function __construct(
        Context $context,
        EuropeanCountries $europeanCountriesSource,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->europeanCountriesSource = $europeanCountriesSource;
    }

    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->europeanCountriesSource->toOptionArray() as $country) {
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