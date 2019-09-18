<?php

namespace AdeoWeb\Dpd\Plugin\Controller\Adminhtml\Order\Shipment;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader;

class PrintLabel
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ShipmentLoader
     */
    private $shipmentLoader;

    /**
     * @var PrintLabelManagementInterface
     */
    private $printLabelManagement;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    public function __construct(
        RequestInterface $request,
        ShipmentLoader $shipmentLoader,
        PrintLabelManagementInterface $printLabelManagement,
        FileFactory $fileFactory,
        ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->shipmentLoader = $shipmentLoader;
        $this->printLabelManagement = $printLabelManagement;
        $this->fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Shipping\Controller\Adminhtml\Order\Shipment\PrintLabel $subject
     * @param callable $proceed
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function aroundExecute(
        \Magento\Shipping\Controller\Adminhtml\Order\Shipment\PrintLabel $subject,
        callable $proceed
    ) {
        $shipment = $this->loadShipment();

        if (strpos($shipment->getOrder()->getShippingMethod(), 'dpd_') === false) {
            return $proceed();
        }

        try {
            $result = $this->printLabelManagement->printLabels($this->getShipmentParcels($shipment));

            return $this->fileFactory->create(
                'ShippingLabel(' . $shipment->getIncrementId() . ').pdf',
                $result,
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $proceed();
        }
    }

    /**
     * @return bool|Shipment
     * @throws LocalizedException
     */
    private function loadShipment()
    {
        $this->shipmentLoader->setOrderId($this->request->getParam('order_id'));
        $this->shipmentLoader->setShipmentId($this->request->getParam('shipment_id'));
        $this->shipmentLoader->setShipment($this->request->getParam('shipment'));
        $this->shipmentLoader->setTracking($this->request->getParam('tracking'));

        return $this->shipmentLoader->load();
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
