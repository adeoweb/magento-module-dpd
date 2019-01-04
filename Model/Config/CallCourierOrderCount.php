<?php

namespace AdeoWeb\Dpd\Model\Config;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;

class CallCourierOrderCount
{
    const XML_PATH_DPD_CALL_COURIER_ORDER_COUNT = 'carriers/dpd/call_courier_order_count';

    /**
     * @var CollectionFactory
     */
    private $configCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    public function __construct(
        CollectionFactory $configCollectionFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
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

    public function register()
    {
        $value = $this->get();

        $this->configWriter->save(self::XML_PATH_DPD_CALL_COURIER_ORDER_COUNT, ($value + 1));

        return $value;
    }
}