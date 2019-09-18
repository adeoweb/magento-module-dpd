<?php

namespace AdeoWeb\Dpd\Model\Carrier\Validator;

use AdeoWeb\Dpd\Model\Carrier\ValidatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Country implements ValidatorInterface
{
    const XML_PATH_METHOD_SALLOWSPECIFIC = 'carriers/dpd/%s/sallowspecific';
    const XML_PATH_METHOD_SPECIFICCOUNTRY = 'carriers/dpd/%s/specificcountry';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param array $context
     * @return boolean
     * @throws \Exception
     */
    public function validate(array $context)
    {
        if (!isset($context['request'], $context['method_code'])) {
            throw new \Exception('Invalid validator data.');
        }

        $methodCode = $context['method_code'];
        $request = $context['request'];

        if ($this->isSpecificCountriesAllowed($methodCode)) {
            $availableCountries = [];

            if ($this->getSpecificCountries($methodCode)) {
                $availableCountries = explode(',', $this->getSpecificCountries($methodCode));
            }

            if (empty($availableCountries) || !in_array($request->getDestCountryId(), $availableCountries)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $methodCode
     * @return boolean
     */
    private function isSpecificCountriesAllowed($methodCode)
    {
        return (bool)$this->scopeConfig->getValue(
            sprintf(self::XML_PATH_METHOD_SALLOWSPECIFIC, $methodCode),
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $methodCode
     * @return string
     */
    private function getSpecificCountries($methodCode)
    {
        return $this->scopeConfig->getValue(
            sprintf(self::XML_PATH_METHOD_SPECIFICCOUNTRY, $methodCode),
            ScopeInterface::SCOPE_STORE
        );
    }
}
