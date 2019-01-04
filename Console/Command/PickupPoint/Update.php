<?php

namespace AdeoWeb\Dpd\Console\Command\PickupPoint;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Update extends Command
{
    /**
     * @var \AdeoWeb\Dpd\Model\PickupPointManagement
     */
    private $pickupPointManagement;

    public function __construct(
        \AdeoWeb\Dpd\Model\PickupPointManagement $pickupPointManagement,
        $name = null
    ) {
        parent::__construct($name);

        $this->pickupPointManagement = $pickupPointManagement;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->writeln('Starting DPD Pickup Point list update.');

        $this->pickupPointManagement->update();

        $output->writeln('DPD Pickup Point list was successfully updated!');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('dpd:pickup-point:update');
        $this->setDescription('Update DPD Pickup Point list');

        parent::configure();
    }
}