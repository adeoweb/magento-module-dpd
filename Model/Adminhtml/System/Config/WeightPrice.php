<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\Adminhtml\System\Config;

use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice\SortProcessor;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;

class WeightPrice
{
    const FIELD_WEIGHT_PRICE = 'weight_price';
    const FIELD_PRICE = 'price';
    const FIELD_WEIGHT = 'weight';
    const FIELD_COUNTRY_PRICE_ID = '_countrypriceid';
    const FIELD_ID = '_id';

    /**
     * @var array|null
     */
    private $value;

    /**
     * @var WeightPrice\SortProcessor
     */
    private $sortProcessor;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        SortProcessor $sortProcessor,
        Escaper $escaper
    ) {
        $this->sortProcessor = $sortProcessor;
        $this->escaper = $escaper;
    }

    public function setValue(array $value = []): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): array
    {
        return (array)$this->value;
    }

    public function getWeightPrices(): array
    {
        $weightPrices = $this->getValue();
        $result = [];
        if (empty($weightPrices)) {
            return [];
        }

        foreach ($weightPrices as $countryId => $countryWeightPrice) {
            if (!is_array($countryWeightPrice) || !is_string($countryId)) {
                continue;
            }
            $row = $this->getCountryWeightPriceRow($countryWeightPrice, $countryId);
            if (empty($row)) {
                continue;
            }

            $result = array_merge($result, $row);
        }

        return $result;
    }

    private function getCountryWeightPriceRow(array $countryWeightPrice, string $countryId): array
    {
        $result = [];
        $weightPrice = $countryWeightPrice[self::FIELD_WEIGHT_PRICE] ?? null;
        if (!$weightPrice) {
            return $result;
        }

        $sortedWeightPrice = $this->sortProcessor->processAsc($weightPrice);
        foreach ($sortedWeightPrice as $weightPriceId => $weightPriceData) {
            if (!is_array($weightPriceData) || empty($weightPriceData)) {
                continue;
            }

            $row = $this->getWeightPriceRow($weightPriceData);
            $row[self::FIELD_COUNTRY_PRICE_ID] = $weightPriceId;
            $row[self::FIELD_ID] = $countryId;
            $result[$weightPriceId] = new DataObject($row);
        }

        return $result;
    }

    private function getWeightPriceRow(array $weightPriceData): array
    {
        $result = [];
        foreach ($weightPriceData as $key => $value) {
            $result[$key] = $this->escaper->escapeHtml($value);
        }

        return $result;
    }
}
