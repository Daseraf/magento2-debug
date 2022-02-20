<?php

namespace ClawRock\Debug\Api;

use ClawRock\Debug\Api\Data\ProfileInterface;
use ClawRock\Debug\Model\Profile\Criteria;

interface ProfileRepositoryInterface
{
    /**
     * @param \ClawRock\Debug\Api\Data\ProfileInterface $profile
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     */
    public function save(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     */
    public function getById(string $token): ProfileInterface;

    /**
     * @param \ClawRock\Debug\Api\Data\ProfileInterface $profile
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     */
    public function delete(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return \ClawRock\Debug\Api\ProfileRepositoryInterface
     */
    public function deleteById(string $token): ProfileRepositoryInterface;

    public function find(Criteria $criteria): array;

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \ClawRock\Debug\Api\Data\ProfileInterface
     */
    public function findLatest(): ProfileInterface;
}
