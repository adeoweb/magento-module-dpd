<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Collection\Request;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/**
 * @codeCoverageIgnore
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'AdeoWeb_Dpd::dpd_collection_request';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->prepend(__('DPD Collection Request'));

        return $resultPage;
    }
}