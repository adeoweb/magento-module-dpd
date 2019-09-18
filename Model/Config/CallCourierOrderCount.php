<?php

namespace AdeoWeb\Dpd\Model\Config;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;

class CallCourierOrderCount
{
    const XML_PATH_DPD_CALL_COURIER_ORDER_COUNT = 'carriers/dpd/call_courier_order_count';

    /**
     * @var CollectionFactory
     */
    private $configCollectionFactory;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    public function __construct(
        CollectionFactory $configCollectionFactory,
        WriterInterface $configWriter
    ) {
        $this->configCollectionFactory = $configCollectionFactory;
        $this->configWriter = $configWriter;
    }

    /**
     * @return int
     */
    public function get()
    {
        $collection = $this->configCollectionFactory->create();
        $collection->addFieldToFilter('path', self::XML_PATH_DPD_CALL_COURIER_ORDER_COUNT);

        $config = $collection->getFirstItem();

        if (!$config) {
            return 1;
        }

        return (int)$config->getData('value');
    }

    /**
     * @return int
     */
    public function register()
    {
        $value = $this->get();

        $this->configWriter->save(self::XML_PATH_DPD_CALL_COURIER_ORDER_COUNT, ($value + 1));

        return $value;
    }
}
