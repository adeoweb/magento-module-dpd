<?php

namespace AdeoWeb\Dpd\Api;

interface CollectionRequestManagementInterface
{
    /**
     * @param array $data
     * @return bool
     */
    public function collectionRequest(array $data);
}