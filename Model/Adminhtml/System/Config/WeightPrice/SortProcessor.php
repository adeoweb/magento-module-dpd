<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice;

use AdeoWeb\Dpd\Model\Adminhtml\System\Config\WeightPrice;

class SortProcessor
{
    const FIELD_WEIGHT = WeightPrice::FIELD_WEIGHT;

    public function processAsc(array $data): array
    {
        uasort($data, array($this, 'sortAsc'));

        return $data;
    }

    public function processDesc(array $data): array
    {
        uasort($data, array($this, 'sortDesc'));

        return $data;
    }

    /**
     * @param string|array $a
     * @param string|array $b
     * @return int
     */
    public function sortAsc($a, $b): int
    {
        if (!is_array($a) || !is_array($b)) {
            return 0;
        }

        $aWeight = $a[self::FIELD_WEIGHT] ?? 0;
        $bWeight = $b[self::FIELD_WEIGHT] ?? 0;

        return $aWeight <=> $bWeight;
    }

    /**
     * @param string|array $a
     * @param string|array $b
     * @return int
     */
    public function sortDesc($a, $b): int
    {
        if (!is_array($a) || !is_array($b)) {
            return 0;
        }

        $aWeight = $a[self::FIELD_WEIGHT] ?? 0;
        $bWeight = $b[self::FIELD_WEIGHT] ?? 0;

        return $bWeight <=> $aWeight;
    }
}
