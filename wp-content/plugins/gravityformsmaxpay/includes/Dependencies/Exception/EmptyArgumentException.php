<?php

namespace GF_Maxpay\Dependencies\Exception;

/**
 * Class EmptyArgumentException
 * @package Maxpay\Lib\Exception
 */
class EmptyArgumentException extends GeneralMaxpayException
{
    /**
     * @param string $paramName
     */
    public function __construct(string $paramName)
    {
        parent::__construct(
            sprintf('Passed argument `%s` is empty', $paramName)
        );
    }
}
