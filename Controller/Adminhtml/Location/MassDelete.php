<?php

namespace AdeoWeb\Dpd\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\Data\LocationInterface;
use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use AdeoWeb\Dpd\Model\ResourceModel\Location\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \AdeoWeb\Dpd\Controller\Adminhtml\Location
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ForwardFactory $resultForwardFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LocationRepositoryInterface $locationRepository
    ) {
        parent::__construct($context, $coreRegistry);

        $this->resultForwardFactory = $resultForwardFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @return ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $locationDeleted = 0;

        /** @var LocationInterface $location */
        foreach ($collection->getItems() as $location) {
            $this->locationRepository->delete($location);
            $locationDeleted++;
        }

        if ($locationDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $locationDeleted)
            );
        }

        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('index');
    }
}
