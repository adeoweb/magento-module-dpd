<?php

namespace AdeoWeb\Dpd\Helper\SubjectReader;

use Magento\Framework\Exception\LocalizedException;

abstract class AbstractSubjectReader
{
    /**
     * @param $key
     * @param null $zone
     * @param array $subject
     * @return mixed
     * @throws LocalizedException
     */
    public function read($key, $zone = null, array $subject)
    {
        if ($zone && isset($subject[$zone])) {
            $subject = $subject[$zone];
        }

        if (!isset($subject[$key])) {
            throw new LocalizedException(__('Parameter "%1" is missing from subject', $key));
        }

        return $subject[$key];
    }
}