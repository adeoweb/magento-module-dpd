<?php

namespace AdeoWeb\Dpd\Helper\Locale;

class LocaleSortProcessor
{
    /**
     * @var string
     */
    protected $order;

    /**
     * @var array
     */
    private $char2order;

    public function __construct(string $order)
    {
        $this->order= $order;
    }

    public function sortData(array $items): array
    {
        if (!is_array($items) || empty($items)) {
            return [];
        }

        $pointers = array_keys($items);

        uasort($pointers, [$this, 'cmp']);

        $sortedItems = [];
        foreach ($pointers as $pointer) {
            $sortedItems = array_merge($sortedItems, $items[$pointer]);
        }

        return $sortedItems;
    }

    protected function cmp(string $a, string $b): int
    {
        if ($a == $b) {
            return 0;
        }

        // lazy init mapping
        if (empty($this->char2order)) {
            $order = 1;
            $len = mb_strlen($this->getCharOrder());
            for ($order=0; $order < $len; ++$order)
            {
                $this->char2order[mb_substr($this->getCharOrder(), $order, 1)] = $order;
            }
        }

        $len_a = mb_strlen($a);
        $len_b = mb_strlen($b);
        $max = min($len_a, $len_b);

        for($i=0; $i<$max; ++$i) {
            $char_a = mb_substr($a, $i, 1);
            $char_b = mb_substr($b, $i, 1);

            if ($char_a == $char_b) {
                continue;
            }

            $order_a = (isset($this->char2order[$char_a])) ? $this->char2order[$char_a] : 9999;
            $order_b = (isset($this->char2order[$char_b])) ? $this->char2order[$char_b] : 9999;

            return ($order_a < $order_b) ? -1 : 1;
        }

        return ($len_a < $len_b) ? -1 : 1;
    }

    protected function getCharOrder(): string
    {
        if (empty($this->order)) {
            return '';
        }

        return $this->order;
    }
}
