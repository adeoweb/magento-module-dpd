<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ArgumentInterface
{
    private const XML_PATH_WEIGHT_UNIT = 'general/locale/weight_unit';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getWeightUnit(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_WEBSITES
        );
    }
}
