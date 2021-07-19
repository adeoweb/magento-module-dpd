<?php

declare(strict_types=1);

namespace AdeoWeb\Dpd\Model\Adminhtml\File;

use AdeoWeb\Dpd\Api\PrintLabelManagementInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Message\ManagerInterface;

class CreatePdfProcessor implements CreatePdfProcessorInterface
{
    /**
     * @var PrintLabelManagementInterface
     */
    private $printLabelManagement;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        PrintLabelManagementInterface $printLabelManagement,
        FileFactory $fileFactory
    ) {
        $this->printLabelManagement = $printLabelManagement;
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $parcels, ManagerInterface $messageManager): void
    {
        if (!empty($parcels)) {
            $result = $this->printLabelManagement->printLabels($parcels);

            $this->fileFactory->create(
                'ShippingLabels.pdf',
                $result,
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } else {
            $messageManager->addError(__('There are no shipping labels related to selected orders.'));
        }
    }
}
