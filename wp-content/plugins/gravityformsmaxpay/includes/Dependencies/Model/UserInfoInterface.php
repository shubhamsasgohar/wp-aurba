<?php

namespace GF_Maxpay\Dependencies\Model;

/**
 * Interface UserInfoInterface
 * @package Maxpay\Lib\Model
 */
interface UserInfoInterface
{
    /**
     * @return array
     */
    public function toHashMap(): array;
}
