<?php

namespace Daseraf\Debug\Model\Serializer;

use Daseraf\Debug\Api\Data\ProfileInterface;

class ProfileSerializer
{
    /**
     * @var \Daseraf\Debug\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Daseraf\Debug\Model\Serializer\CollectorSerializer
     */
    private $collectorSerializer;

    /**
     * @var \Daseraf\Debug\Model\ProfileFactory
     */
    private $profileFactory;

    public function __construct(
        \Daseraf\Debug\Serializer\SerializerInterface $serializer,
        \Daseraf\Debug\Model\Serializer\CollectorSerializer $collectorSerializer,
        \Daseraf\Debug\Model\ProfileFactory $profileFactory
    ) {
        $this->serializer = $serializer;
        $this->collectorSerializer = $collectorSerializer;
        $this->profileFactory = $profileFactory;
    }

    public function serialize(ProfileInterface $profile): string
    {
        return $this->serializer->serialize(array_merge(
            $profile->getData(),
            ['collectors' => $this->collectorSerializer->serialize($profile->getCollectors())]
        ));
    }

    public function unserialize(string $data): ProfileInterface
    {
        $profileData = $this->serializer->unserialize($data);
        $collectors = $this->collectorSerializer->unserialize($profileData['collectors']);
        unset($profileData['collectors']);

        /** @var \Daseraf\Debug\Model\Profile $profile */
        $profile = $this->profileFactory->create(['token' => $profileData['token']])->setData($profileData);
        $profile->setCollectors($collectors);

        return $profile;
    }
}
