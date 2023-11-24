<?php

namespace Daseraf\Debug\App;

interface ConfigInterface
{
    /**
     * Retrieve config value by path
     *
     * Path should looks like keys imploded by "/". For example scopes/stores/admin
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path);

    /**
     * Set config value
     *
     * @deprecated 100.1.2
     * @param string $path
     * @param mixed $value
     * @return void
     */
    public function setValue($path, $value);

    /**
     * Retrieve config flag
     *
     * Path should looks like keys imploded by "/". For example scopes/stores/admin
     *
     * @param string $path
     * @return bool
     */
    public function isSetFlag($path);
}
