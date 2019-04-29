<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Order;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use AdeoWeb\Dpd\Helper\Config\Serializer;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassPrintLabels extends Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var CollectionFactory
     */
    private $shipmentCollectionFactory;

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
        OrderCollectionFactory $orderCollectionFactory,
        CollectionFactory $shipmentCollectionFactory,
        PrintLabelManagementInterface $printLabelManagement,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->printLabelManagement = $printLabelManagement;
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        try {
            $orderCollection = $this->orderCollectionFactory->create()->addAttributeToFilter(
                'shipping_method',
                ['like' => 'dpd_%']
            );

            $collection = $this->filter->getCollection($orderCollection);
            $shipments = $this->shipmentCollectionFactory->create()
                ->setOrderFilter(['in' => $collection->getAllIds()]);

            $parcels = [];

            if ($shipments->getSize()) {
                /** @var Shipment $shipment */
                foreach ($shipments as $shipment) {
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

            return $this->resultRedirectFactory->create()->setPath('sales/order/');
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