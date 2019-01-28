<?php


namespace AdeoWeb\Dpd\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        LocationRepositoryInterface $locationRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $request = $this->getRequest();

        if (!$request->getParam('isAjax')) {
            throw new LocalizedException(__('Only AJAX calls are permitted'));
        }

        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();

        $error = false;
        $messages = [];

        $postItems = $request->getParam('items', []);

        if (empty($postItems)) {
            $messages[] = __('Please correct the data sent.');
            $error = true;
        } else {
            foreach (array_keys($postItems) as $locationId) {
                try {
                    $location = $this->locationRepository->getById($locationId);
                    $location->setData(array_merge($location->getData(), $postItems[$locationId]));

                    $this->locationRepository->save($location);
                } catch (\Exception $e) {
                    $messages[] = "[Location ID: {$locationId}] {$e->getMessage()}";
                    $error = true;
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
