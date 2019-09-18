<?php

namespace AdeoWeb\Dpd\Helper\SubjectReader;

use Magento\Framework\Exception\LocalizedException;

class CallCourierRequest extends AbstractSubjectReader
{
    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readWarehouseId($subject)
    {
        return $this->read('warehouse', null, $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readPickupDate($subject)
    {
        return $this->read('pickup_date', null, $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readPickupTime($subject)
    {
        return $this->read('pickup_time', null, $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readWorkUntil($subject)
    {
        return $this->read('work_until', null, $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     */
    public function readTotalWeight($subject)
    {
        return (float)$this->read('total_weight', null, $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     */
    public function readNumOfParcels($subject)
    {
        return (int)$this->read('num_of_parcels', null, $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readComment($subject)
    {
        return $this->read('comment', null, $subject);
    }
}
