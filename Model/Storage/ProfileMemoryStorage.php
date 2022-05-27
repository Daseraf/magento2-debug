<?php

namespace Daseraf\Debug\Model\Storage;

use Daseraf\Debug\Api\Data\ProfileInterface;

class ProfileMemoryStorage
{
    /**
     * @var \Daseraf\Debug\Model\Profile
     */
    private $profile;

    public function read(): ProfileInterface
    {
        return $this->profile;
    }

    public function write(ProfileInterface $profile)
    {
        $this->profile = $profile;
    }
}
