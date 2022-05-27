<?php

namespace Daseraf\Debug\Plugin\ProfileRepository;

use Daseraf\Debug\Api\Data\ProfileInterface;
use Daseraf\Debug\Api\ProfileRepositoryInterface;
use Daseraf\Debug\Model\Collector\TimeCollector;

class RequestTimePlugin
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Daseraf\Debug\Api\ProfileRepositoryInterface $subject
     * @param \Daseraf\Debug\Api\Data\ProfileInterface $profile
     * @return array
     */
    public function beforeSave(ProfileRepositoryInterface $subject, ProfileInterface $profile)
    {
        try {
            /** @var \Daseraf\Debug\Model\Collector\TimeCollector $timeCollector */
            $timeCollector = $profile->getCollector(TimeCollector::NAME);
        } catch (\InvalidArgumentException $e) {
            return [$profile];
        }

        $profile->setRequestTime($timeCollector->getDuration());

        return [$profile];
    }
}
