<?php

namespace AdeoWeb\Dpd\Model\ResourceModel;

use AdeoWeb\Dpd\Api\Data\PickupPointInterface;
use AdeoWeb\Dpd\Setup\SchemaInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @codeCoverageIgnore
 */
class PickupPoint extends AbstractDb
{
    protected $_serializableFields = ['opening_hours' => [null, []]];

    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init(
            SchemaInterface::TABLE_DPD_PICKUP_POINT,
            PickupPointInterface::PICKUP_POINT_ID
        );
    }

    /**
     * @param AbstractModel $object
     * @return bool
     */
    public function isModified(AbstractModel $object)
    {
        if (!$this->isDataUpdated($object)) {
            $object->setHasDataChanges(false);
        }

        return parent::isModified($object);
    }

    /**
     * @param AbstractModel $object
     * @return bool
     */
    protected function isDataUpdated(AbstractModel $object)
    {
        if (!$object->getId()) {
            return true;
        }

        foreach ($object->getOrigData() as $key => $entry) {
            if ($object->getData($key) != $entry) {
                return true;
            }
        }

        return false;
    }
}
