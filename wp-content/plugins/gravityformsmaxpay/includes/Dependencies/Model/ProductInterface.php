<?php

namespace GF_Maxpay\Dependencies\Model;

/**
 * Interface ProductInterface
 * @package Maxpay\Lib\Model
 */
interface ProductInterface
{
    /**
     * @return array
     */
    public function toHashMap(): array;
}
