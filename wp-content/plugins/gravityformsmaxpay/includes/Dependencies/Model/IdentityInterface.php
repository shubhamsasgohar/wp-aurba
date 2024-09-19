<?php

namespace GF_Maxpay\Dependencies\Model;

/**
 * Interface IdentityInterface
 * @package Maxpay\Lib\Model
 */
interface IdentityInterface
{
    /**
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * @return string
     */
    public function getPrivateKey(): string;
}
