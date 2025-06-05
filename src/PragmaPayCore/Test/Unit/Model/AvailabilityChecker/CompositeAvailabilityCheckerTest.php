<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Model\AvailabilityChecker;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\CompositeAvailabilityChecker;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\AvailabilityCheckerInterface;
use Magento\Quote\Model\Quote;

class CompositeAvailabilityCheckerTest extends TestCase
{
    public function testExecuteReturnsTrueWhenAllCheckersReturnTrue()
    {
        $checker1 = $this->createMock(AvailabilityCheckerInterface::class);
        $checker1->method('execute')->willReturn(true);

        $checker2 = $this->createMock(AvailabilityCheckerInterface::class);
        $checker2->method('execute')->willReturn(true);

        $quote = $this->createMock(Quote::class);

        $compositeChecker = new CompositeAvailabilityChecker([$checker1, $checker2]);

        $this->assertTrue($compositeChecker->execute($quote));
    }

    public function testExecuteReturnsFalseWhenAnyCheckerReturnsFalse()
    {
        $checker1 = $this->createMock(AvailabilityCheckerInterface::class);
        $checker1->method('execute')->willReturn(true);

        $checker2 = $this->createMock(AvailabilityCheckerInterface::class);
        $checker2->method('execute')->willReturn(false);

        $quote = $this->createMock(Quote::class);

        $compositeChecker = new CompositeAvailabilityChecker([$checker1, $checker2]);

        $this->assertFalse($compositeChecker->execute($quote));
    }
}
