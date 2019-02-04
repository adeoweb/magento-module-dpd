<?php

namespace AdeoWeb\Dpd\Model\App\Request;

interface DataPersistorInterface
{
    /**
     * Store data by key
     *
     * @param string $key
     * @param mixed $data
     * @return void
     * @since 100.1.0
     */
    public function set($key, $data);

    /**
     * Retrieve data by key
     *
     * @param string $key
     * @return mixed
     * @since 100.1.0
     */
    public function get($key);

    /**
     * Clear data by key
     *
     * @param string $key
     * @return void
     * @since 100.1.0
     */
    public function clear($key);
}
