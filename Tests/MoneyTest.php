<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2 0002
 * Time: 14:30
 */

namespace Tests;

class MoneyTest extends \PHPUnit\Framework\TestCase
{

    public function testCanBeNegated()
    {
        $a = new Money(1);

        $b = $a->negate();

        $this->assertEquals(-1, $b->getAmount());
    }

    public function testBoolean()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function equal()
    {
        $this->assertEquals(2, 1 + 1);
    }
}