<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Shipment;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassPrintLabels extends Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var PrintLabelManagementInterface
     */
    private $printLabelManagement;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $shipmentCollectionFactory,
        PrintLabelManagementInterface $printLabelManagement,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $shipmentCollectionFactory;
        $this->printLabelManagement = $printLabelManagement;
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $parcels = [];

            if ($collection->getSize()) {
                /** @var Shipment $shipment */
                foreach ($collection as $shipment) {
                    if (strpos($shipment->getOrder()->getShippingMethod(), 'dpd_') === false) {
                        continue;
                    }

                    $parcels = \array_merge($parcels, $this->getShipmentParcels($shipment));
                }
            }

            $result = $this->printLabelManagement->printLabels($parcels);

            return $this->fileFactory->create(
                'ShippingLabels.pdf',
                $result,
                DirectoryList::VAR_DIR,
                'application/pdf'
            );

        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $this->resultRedirectFactory->create()->setPath('sales/shipment/');
        }
    }

    /**
     * @param Shipment $shipment
     * @return array
     */
    private function getShipmentParcels(Shipment $shipment)
    {
        $tracks = $shipment->getTracks();

        $result = [];

        foreach ($tracks as $shipmentTrack) {
            $result[] = $shipmentTrack->getTrackNumber();
        }

        return $result;
    }
}