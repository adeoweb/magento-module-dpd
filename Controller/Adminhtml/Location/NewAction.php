<?php


namespace AdeoWeb\Dpd\Controller\Adminhtml\Location;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;

class NewAction extends \AdeoWeb\Dpd\Controller\Adminhtml\Location
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $coreRegistry);

        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
