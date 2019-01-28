<?php

namespace AdeoWeb\Dpd\Config\Classic;

use AdeoWeb\Dpd\Helper\Config\Serializer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Restrictions
{
    const XML_PATH_METHOD_CLASSIC_RESTRICTIONS = 'carriers/dpd/classic/restrictions';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Serializer $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @param $countryId
     * @return null|int|string
     * @throws \Exception
     */
    public function getByCountry($countryId)
    {
        $configValue = $this->getConfigValue();

        if (!\is_array($configValue)) {
            throw new \Exception('Invalid method configuration');
        }

        $index = \array_search($countryId, \array_column($configValue, 'country'));

        if ($index === false) {
            return null;
        }

        return $configValue[$index];
    }

    /**
     * @return array
     */
    protected function getConfigValue()
    {
        $configValue = $this->scopeConfig->getValue(
            self::XML_PATH_METHOD_CLASSIC_RESTRICTIONS,
            ScopeInterface::SCOPE_WEBSITE
        );

        $result = $this->serializer->unserialize($configValue);

        return \is_array($result) ? \array_values($result) : $result;
    }
}