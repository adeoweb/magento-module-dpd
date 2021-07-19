<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Shipment;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
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
     * @var CollectionFactory
     */
    private $collectionFactory;

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
        CollectionFactory $shipmentCollectionFactory,
        PrintLabelManagementInterface $printLabelManagement,
        CreatePdfProcessorInterface $createPdfProcessor
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $shipmentCollectionFactory;
        $this->printLabelManagement = $printLabelManagement;
        $this->createPdfProcessor = $createPdfProcessor;
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

                    $parcels = array_merge($parcels, $this->getShipmentParcels($shipment));
                }
            }

            $this->createPdfProcessor->process($parcels, $this->messageManager);

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath('sales/order/');
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath('sales/shipment/');
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
