<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Order;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use Magento\Backend\App\Action;
use Exception;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use AdeoWeb\Dpd\Model\Adminhtml\File\CreatePdfProcessorInterface;

use function array_merge;

class MassPrintDpdLabels extends Action
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
     * @var CreatePdfProcessorInterface
     */
    private $createPdfProcessor;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        OrderCollectionFactory $orderCollectionFactory,
        CollectionFactory $shipmentCollectionFactory,
        PrintLabelManagementInterface $printLabelManagement,
        CreatePdfProcessorInterface $createPdfProcessor
    ) {
        $this->filter = $filter;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->printLabelManagement = $printLabelManagement;
        $this->createPdfProcessor = $createPdfProcessor;

        parent::__construct($context);
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
                foreach ($shipments as $shipment) {
                    $parcels[] = $this->getShipmentParcels($shipment);
                }
            }

            $this->createPdfProcessor->process(array_merge(...$parcels), $this->messageManager);

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath('sales/order/');
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath('sales/order/');
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
