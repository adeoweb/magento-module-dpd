<?php

namespace AdeoWeb\Dpd\Model\ResourceModel;

use AdeoWeb\Dpd\Setup\Schema;
use Magento\Framework\Model\AbstractModel;

class Location extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Schema::TABLE_DPD_LOCATION, 'location_id');
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function _beforeSave(AbstractModel $object)
    {
        $object->setData('updated_at', new \DateTime);

        if ($object->isObjectNew()) {
            $object->setData('created_at', new \DateTime);
        }

        return $this;
    }
}
