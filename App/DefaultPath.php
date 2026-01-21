<?php
/**
 * Designed by Stanislav Matiavin
 */

declare(strict_types=1);
namespace Daseraf\Debug\App;

class DefaultPath implements \Magento\Framework\App\DefaultPathInterface
{
    /**
     * @var array
     */
    protected $parts;

    /**
     * @param ConfigInterface $config
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(\Daseraf\Debug\App\ConfigInterface $config)
    {
        $pathParts = explode('/', (string) $config->getValue('web/default/debug'));

        $this->parts = [
            'area' => $pathParts[0] ?? '',
            'module' => $pathParts[1] ?? 'debug',
            'controller' => $pathParts[2] ?? 'index',
            'action' => $pathParts[3] ?? 'index',
        ];
    }

    /**
     * Retrieve default path part by code
     *
     * @param string $code
     * @return string
     */
    public function getPart($code)
    {
        return $this->parts[$code] ?? null;
    }
}
