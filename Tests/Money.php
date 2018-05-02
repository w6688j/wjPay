<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2 0002
 * Time: 14:30
 */

namespace Tests;

class Money
{

    private $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function negate()
    {
        return new Money(-1 * $this->amount);
    }
}