<?php

namespace AdeoWeb\Dpd\Helper\SubjectReader;

class CollectionRequestRequest extends AbstractSubjectReader
{
    /**
     * @param array $subject
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readOrderId($subject)
    {
        return $this->read('order_id', null, $subject);
    }


    /**
     * @param array $subject
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readIsSenderUseShippingAddress($subject)
    {
        return $this->read('sender_use_shipping_address', 'sender_adress', $subject) == '1' ?: false;
    }

    /**
     * @param array $subject
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readSenderLocation($subject)
    {
        return $this->read('sender_location', 'sender_adress', $subject);
    }

    /**
     * @param array $subject
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readIsRecipientUseShippingAddress($subject)
    {
        return $this->read('recipient_use_shipping_address', 'recipient_adress', $subject) == '1' ?: false;
    }

    /**
     * @param array $subject
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readRecipientLocation($subject)
    {
        return $this->read('recipient_location', 'recipient_adress', $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readPickupDate($subject)
    {
        return $this->read('pickup_date', 'package_info', $subject);
    }

    /**
     * @param array $subject
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readTotalWeight($subject)
    {
        return (float)$this->read('total_weight', 'package_info', $subject);
    }

    /**
     * @param array $subject
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readNumOfParcels($subject)
    {
        return (int)$this->read('num_of_parcels', 'package_info', $subject);
    }

    /**
     * @param array $subject
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readComment($subject)
    {
        return $this->read('comment', 'package_info', $subject);
    }
}