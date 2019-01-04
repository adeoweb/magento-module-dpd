<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

abstract class Location extends \Magento\Backend\App\Action
{
    protected $coreRegistry;

    const ADMIN_RESOURCE = 'AdeoWeb_Dpd::adeoweb_dpd_location';

    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        parent::__construct($context);

        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage)
    {
        $resultPage
            ->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('AdeoWeb'), __('AdeoWeb'))
            ->addBreadcrumb(__('Location'), __('Location'));

        return $resultPage;
    }
}
