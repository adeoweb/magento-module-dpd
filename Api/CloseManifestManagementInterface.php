<?php

namespace AdeoWeb\Dpd\Api;

interface CloseManifestManagementInterface
{
    /**
     * @return array
     * @throws \Exception
     */
    public function closeManifest();
}