<?php


namespace AdeoWeb\Dpd\Controller\Adminhtml\Location;

use AdeoWeb\Dpd\Api\LocationRepositoryInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

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
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $jsonFactory,
        LocationRepositoryInterface $locationRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();

        $error = false;
        $messages = [];

        $request = $this->getRequest();

        if ($request->getParam('isAjax')) {
            $postItems = $request->getParam('items', []);

            if (empty($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $locationId) {
                    $location = $this->locationRepository->getById($locationId);

                    try {
                        $location->setData(array_merge($location->getData(), $postItems[$locationId]));

                        $this->locationRepository->save($location);
                    } catch (\Exception $e) {
                        $messages[] = "[Location ID: {$locationId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
