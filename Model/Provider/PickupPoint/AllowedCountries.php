<?php

namespace AdeoWeb\Dpd\Model\Provider\PickupPoint;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AllowedCountries
{
    const XML_PATH_METHOD_SALLOWSPECIFIC = 'carriers/dpd/pickup/sallowspecific';
    const XML_PATH_METHOD_SPECIFICCOUNTRY = 'carriers/dpd/pickup/specificcountry';
    const XML_PATH_EU_COUNTRIES = 'general/country/eu_countries';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function get()
    {
        $countryList = $this->isSpecificCountriesAllowed() ? $this->getSpecificCountries() : $this->getEuCountries();

        return \array_filter(\explode(',', $countryList));
    }

    /**
     * @return boolean
     */
    private function isSpecificCountriesAllowed()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_METHOD_SALLOWSPECIFIC,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    private function getSpecificCountries()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_METHOD_SPECIFICCOUNTRY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    private function getEuCountries()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EU_COUNTRIES);
    }
}
