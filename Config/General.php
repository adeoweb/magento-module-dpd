<?php

namespace AdeoWeb\Dpd\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @codeCoverageIgnore
 */
class General
{
    const XML_PATH_PRINT_LABEL_FORMAT = 'adeoweb_dpd/general/print_label_format';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return null|string
     */
    public function getPrintLabelFormat()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRINT_LABEL_FORMAT,
            ScopeInterface::SCOPE_WEBSITES
        );
    }
}