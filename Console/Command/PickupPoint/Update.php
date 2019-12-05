<?php

namespace AdeoWeb\Dpd\Console\Command\PickupPoint;

use AdeoWeb\Dpd\Model\PickupPointManagement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class Update extends Command
{
    /**
     * @var PickupPointManagement
     */
    private $pickupPointManagement;

    public function __construct(
        PickupPointManagement $pickupPointManagement,
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
        $output->setDecorated(true);
        $output->writeln('Starting DPD Pickup Point list update.');

        $result = $this->pickupPointManagement->update();

        if (!\is_array($result)) {
            $output->writeln('DPD Pickup Point list was successfully updated! Do not forget to clean Magento cache.');

            return true;
        }

        foreach ($result as $languageCode => $warning) {
            $output->writeln(
                \sprintf(
                    '<error>Error encountered while updating list for "%s": %s</error>',
                    $languageCode,
                    $warning
                )
            );
        }

        return true;
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
