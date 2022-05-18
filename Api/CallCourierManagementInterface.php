<?php

namespace AdeoWeb\Dpd\Api;

interface CallCourierManagementInterface
{
    /**
     * Public method
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function callCourier(array $data);
}
