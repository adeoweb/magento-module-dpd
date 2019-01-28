<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Action;

use AdeoWeb\Dpd\Api\CloseManifestManagementInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

class CloseManifest extends Action implements HttpGetActionInterface
{
    /**
     * @var CloseManifestManagementInterface
     */
    private $closeManifestManagement;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Action\Context $context,
        CloseManifestManagementInterface $closeManifestManagement,
        JsonFactory $resultJsonFactory,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->closeManifestManagement = $closeManifestManagement;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->fileFactory = $fileFactory;
    }

    /**
     * @return ResultInterface|ResponseInterface
     * @throws \Zend_Pdf_Exception
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $pdfList = $this->closeManifestManagement->closeManifest();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->_redirect('sales/shipment/index');
        }

        $outputPdf = new \Zend_Pdf();
        foreach ($pdfList as $content) {
            $pdfLabel = \Zend_Pdf::parse($content);
            foreach ($pdfLabel->pages as $page) {
                $outputPdf->pages[] = clone $page;
            }
        }

        return $this->fileFactory->create(
            'DPD_Manifest_Print(' . (new \DateTime())->format('Y-m-d-H-i-s'). ').pdf',
            $outputPdf->render(),
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('AdeoWeb_Dpd::dpd_close_manifest');
    }
}