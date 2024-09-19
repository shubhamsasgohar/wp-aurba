<?php

namespace GF_Maxpay\Dependencies\Model;

/**
 * Interface RenderableInterface
 * @package Maxpay\Lib\Model
 */
interface RenderableInterface
{
    /**
     * @return string
     */
    public function asString(): string;

    /**
     * @return void
     */
    public function display(): void;
}
