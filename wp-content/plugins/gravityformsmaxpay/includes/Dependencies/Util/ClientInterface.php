<?php

namespace GF_Maxpay\Dependencies\Util;

/**
 * Interface ClientInterface
 * @package Maxpay\Lib\Util
 */
interface ClientInterface
{
    /**
     * @param mixed[] $data
     * @return mixed[]
     * @throws GeneralMaxpayException
     */
    public function send(array $data);
}
