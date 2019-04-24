<?php

namespace AdeoWeb\Dpd\Helper\SubjectReader;

use Magento\Framework\Exception\LocalizedException;

class CollectionRequestRequest extends AbstractSubjectReader
{
    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     */
    public function readOrderId($subject)
    {
        return $this->read('order_id', null, $subject, true);
    }

    /**
     * @param array $subject
     * @return bool
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readIsSenderUseShippingAddress($subject)
    {
        return $this->read('sender_use_shipping_address', 'sender_adress', $subject) == '1' ?: false;
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readSenderLocation($subject)
    {
        return $this->read('sender_location', 'sender_adress', $subject);
    }

    /**
     * @param array $subject
     * @return bool
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readIsRecipientUseShippingAddress($subject)
    {
        return $this->read('recipient_use_shipping_address', 'recipient_adress', $subject) == '1' ?: false;
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readRecipientLocation($subject)
    {
        return $this->read('recipient_location', 'recipient_adress', $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readPickupDate($subject)
    {
        return $this->read('pickup_date', 'package_info', $subject);
    }

    /**
     * @param array $subject
     * @return float
     * @throws LocalizedException
     */
    public function readTotalWeight($subject)
    {
        return (float)$this->read('total_weight', 'package_info', $subject);
    }

    /**
     * @param array $subject
     * @return int
     * @throws LocalizedException
     */
    public function readNumOfParcels($subject)
    {
        return (int)$this->read('num_of_parcels', 'package_info', $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws LocalizedException
     * @codeCoverageIgnore
     */
    public function readComment($subject)
    {
        return $this->read('comment', 'package_info', $subject);
    }
}