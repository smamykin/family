<?php

namespace App\Tests;

use App\Services\PromotionCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PromotionCalculatorTest extends TestCase
{
    public function testSomething()
    {
        /** @var MockObject|PromotionCalculator $calc */
        $calc = $this->getMockBuilder(PromotionCalculator::class)
            ->setMethods(['getPromotionPercentage'])
            ->getMock();

        $calc->expects($this->any())
            ->method('getPromotionPercentage')
            ->willReturn(20);

        $result = $calc->calculatePriceAfterPromotion(1,9);
        $this->assertSame(8, $result);

        $result = $calc->calculatePriceAfterPromotion(10, 20, 50);
        $this->assertSame(64, $result);
    }
}
