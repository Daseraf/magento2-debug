<?php

namespace Daseraf\Debug\Api;

use Daseraf\Debug\Api\Data\ProfileInterface;
use Daseraf\Debug\Model\Profile\Criteria;

interface ProfileRepositoryInterface
{
    /**
     * @param \Daseraf\Debug\Api\Data\ProfileInterface $profile
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    public function save(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Daseraf\Debug\Api\Data\ProfileInterface
     */
    public function getById(string $token): ProfileInterface;

    /**
     * @param \Daseraf\Debug\Api\Data\ProfileInterface $profile
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    public function delete(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    public function deleteById(string $token): ProfileRepositoryInterface;

    public function find(Criteria $criteria): array;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Daseraf\Debug\Api\Data\ProfileInterface
     */
    public function findLatest(): ProfileInterface;
}
