<?php

namespace AdeoWeb\Dpd\Model\Carrier\Validator\Saturday;

use AdeoWeb\Dpd\Model\Carrier\ValidatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;

class Timeframe implements ValidatorInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        TimezoneInterface $timezone
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->timezone = $timezone;
    }

    /**
     * @param array $context
     * @return boolean
     * @throws \Exception
     */
    public function validate(array $context)
    {
        if (!isset($context['request'], $context['method_code'])) {
            throw new \Exception('Invalid validator data.');
        }

        $timezone = new \DateTimeZone($this->timezone->getConfigTimezone());
        $now = new \DateTime('now', $timezone);

        $availableTimes = \explode(',', $this->getAvailableTimesConfig($now->format('l')));
        $availableTimes = \array_filter($availableTimes);

        foreach ($availableTimes as $availableTime) {
            $startTime = \DateTime::createFromFormat('H:i', $availableTime, $timezone);
            $endTime = \DateTime::createFromFormat('H:i', $availableTime, $timezone);
            $endTime->modify('+30 minutes');

            if ($now > $startTime && $now < $endTime) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $dayOfWeek
     * @return string
     */
    private function getAvailableTimesConfig($dayOfWeek)
    {
        $dayOfWeek = \strtolower($dayOfWeek);

        return $this->scopeConfig->getValue(
            \sprintf('carriers/dpd/saturday/timeframe/%s', $dayOfWeek),
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}