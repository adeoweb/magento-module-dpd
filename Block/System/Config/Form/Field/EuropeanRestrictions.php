<?php

namespace AdeoWeb\Dpd\Block\System\Config\Form\Field;

use AdeoWeb\Dpd\Block\Adminhtml\Form\Field\Country;
use AdeoWeb\Dpd\Block\Adminhtml\Form\Field\EuropeanCountry;
use Magento\Framework\Exception\LocalizedException;

class EuropeanRestrictions extends Restrictions
{
    /**
     * @return Country
     * @throws LocalizedException
     */
    protected function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                EuropeanCountry::class, '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->countryRenderer;
    }
}