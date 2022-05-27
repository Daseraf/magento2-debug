<?php

declare(strict_types=1);

namespace Daseraf\Debug\Model;

use Daseraf\Debug\Api\Data\ProfileInterface;
use Daseraf\Debug\Api\ProfileRepositoryInterface;
use Daseraf\Debug\Model\Profile\Criteria;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

class ProfileRepository implements ProfileRepositoryInterface
{
    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileFileStorage
     */
    private $fileStorage;

    /**
     * @var \Daseraf\Debug\Model\Profile\CriteriaFactory
     */
    private $criteriaFactory;

    public function __construct(
        \Daseraf\Debug\Model\Storage\ProfileFileStorage $fileStorage,
        \Daseraf\Debug\Model\Profile\CriteriaFactory $criteriaFactory
    ) {
        $this->fileStorage = $fileStorage;
        $this->criteriaFactory = $criteriaFactory;
    }

    /**
     * @param \Daseraf\Debug\Api\Data\ProfileInterface $profile
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    public function save(ProfileInterface $profile): ProfileRepositoryInterface
    {
        try {
            $this->fileStorage->write($profile);

            return $this;
        } catch (FileSystemException $e) {
            throw new CouldNotSaveException(__('Profile could not be saved.'));
        }
    }

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Daseraf\Debug\Api\Data\ProfileInterface
     */
    public function getById(string $token): ProfileInterface
    {
        try {
            return $this->fileStorage->read($token);
        } catch (FileSystemException $e) {
            throw new NoSuchEntityException(__('Profile with token %s doesn\'t exist.', $token));
        }
    }

    /**
     * @param \Daseraf\Debug\Api\Data\ProfileInterface $profile
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    public function delete(ProfileInterface $profile): ProfileRepositoryInterface
    {
        try {
            $this->fileStorage->remove($profile->getToken());

            return $this;
        } catch (FileSystemException $e) {
            throw new CouldNotDeleteException(__('Profile with token %s could not be deleted.', $profile->getToken()));
        }
    }

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    public function deleteById(string $token): ProfileRepositoryInterface
    {
        try {
            $this->fileStorage->remove($token);

            return $this;
        } catch (FileSystemException $e) {
            throw new CouldNotDeleteException(__('Profile with token %s could not be deleted.', $token));
        }
    }

    public function find(Criteria $criteria): array
    {
        return $this->fileStorage->find($criteria);
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Daseraf\Debug\Api\Data\ProfileInterface
     */
    public function findLatest(): ProfileInterface
    {
        try {
            /** @var \Daseraf\Debug\Model\Profile\Criteria $criteria */
            $criteria = $this->criteriaFactory->create(['limit' => 1]);

            $results = $this->fileStorage->find($criteria);
            $token = reset($results)->getToken();

            return $this->fileStorage->read($token);
        } catch (FileSystemException $e) {
            throw new NoSuchEntityException(__('Could not find latest token'));
        }
    }
}
