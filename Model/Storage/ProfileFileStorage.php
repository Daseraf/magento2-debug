<?php

namespace Daseraf\Debug\Model\Storage;

use Daseraf\Debug\Api\Data\ProfileInterface;
use Daseraf\Debug\Model\Profile\Criteria;
use Daseraf\Debug\Model\ValueObject\SearchResult;
use Magento\Framework\Exception\FileSystemException;

class ProfileFileStorage
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileSystem;

    /**
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    private $fileReadFactory;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    private $fileWriteFactory;

    /**
     * @var \Daseraf\Debug\Logger\Logger
     */
    private $logger;

    /**
     * @var \Daseraf\Debug\Helper\File
     */
    private $fileHelper;

    /**
     * @var \Daseraf\Debug\Model\ProfileFactory
     */
    private $profileFactory;

    /**
     * @var \Daseraf\Debug\Model\Serializer\ProfileSerializer
     */
    private $profileSerializer;

    /**
     * @var \Daseraf\Debug\Model\Indexer\ProfileIndexer
     */
    private $profileIndexer;

    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileSystem,
        \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWriteFactory,
        \Daseraf\Debug\Logger\Logger $logger,
        \Daseraf\Debug\Helper\File $fileHelper,
        \Daseraf\Debug\Model\ProfileFactory $profileFactory,
        \Daseraf\Debug\Model\Serializer\ProfileSerializer $profileSerializer,
        \Daseraf\Debug\Model\Indexer\ProfileIndexer $profileIndexer
    ) {
        $this->fileSystem = $fileSystem;
        $this->fileReadFactory = $fileReadFactory;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->logger = $logger;
        $this->fileHelper = $fileHelper;
        $this->profileFactory = $profileFactory;
        $this->profileSerializer = $profileSerializer;
        $this->profileIndexer = $profileIndexer;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param \Daseraf\Debug\Model\Profile\Criteria $criteria
     * @return array
     */
    public function find(Criteria $criteria): array
    {
        $results = [];

        try {
            if (!$this->fileSystem->isExists($this->fileHelper->getProfileIndex())) {
                return $results;
            }

            $resource = $this->fileSystem->fileOpen($this->fileHelper->getProfileIndex(), 'r');
            $i = 0;
            while ($profile = $this->fileSystem->fileGetCsv($resource)) {
                if ($criteria->match($profile)) {
                    $results[] = SearchResult::createFromCsv($profile);
                    if (++$i >= $criteria->getLimit()) {
                        break;
                    }
                }
            }

            $this->fileSystem->fileClose($resource);
        } catch (FileSystemException $e) {
            $this->logger->critical($e);
        }

        return $results;
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function purge()
    {
        $this->fileSystem->deleteDirectory($this->fileHelper->getProfileDirectory());
    }

    /**
     * @param $token
     * @throws \Magento\Framework\Exception\FileSystemException
     * @return \Daseraf\Debug\Api\Data\ProfileInterface
     */
    public function read($token): ProfileInterface
    {
        $file = $this->fileReadFactory->create($this->fileHelper->getProfileFilename($token), $this->fileSystem);

        return $this->profileSerializer->unserialize($file->readAll());
    }

    /**
     * @param \Daseraf\Debug\Api\Data\ProfileInterface $profile
     * @throws \Magento\Framework\Exception\FileSystemException
     * @return string
     */
    public function write(ProfileInterface $profile)
    {
        $path = $this->fileHelper->getProfileFilename($profile->getToken());
        $this->fileSystem->createDirectory($this->fileSystem->getParentDirectory($path));
        $file = $this->fileWriteFactory->create($path, $this->fileSystem, 'w');
        $file->write($this->profileSerializer->serialize($profile));
        $file->close();
        $profile->setFilesize($this->fileSystem->stat($path)['size']);

        $this->profileIndexer->index($profile);

        return $path;
    }

    /**
     * @param string $token
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function remove(string $token)
    {
        $path = $this->fileHelper->getProfileFilename($token);
        $this->fileSystem->deleteFile($path);
    }
}
