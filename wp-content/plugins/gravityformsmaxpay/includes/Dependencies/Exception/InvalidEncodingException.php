<?php

namespace GF_Maxpay\Dependencies\Exception;

/**
 * Class InvalidEncodingException
 * @package Maxpay\Lib\Exception
 */
class InvalidEncodingException extends GeneralMaxpayException
{
    /**
     * @param string $paramName
     */
    public function __construct(string $paramName)
    {
        parent::__construct(
            sprintf('Passed argument `%s` has wrong encoding', $paramName)
        );
    }
}
