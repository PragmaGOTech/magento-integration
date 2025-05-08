<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Model\AvailabilityChecker;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\GrandTotalThreshold;
use Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface;
use Magento\Quote\Model\Quote;

class GrandTotalThresholdTest extends TestCase
{
    private $cartConfigProvider;
    private $quote;
    private $checker;

    protected function setUp(): void
    {
        $this->cartConfigProvider = $this->createMock(PragmaPayCartConfigProviderInterface::class);
        $this->cartConfigProvider->method('getMinOrderTotal')->willReturn(50.0);
        $this->cartConfigProvider->method('getMaxOrderTotal')->willReturn(200.0);

        $this->quote = $this->getMockBuilder(Quote::class)
            ->addMethods(['getBaseGrandTotal'])
            ->onlyMethods(['getStoreId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->quote->method('getStoreId')->willReturn(1);

        $this->checker = new GrandTotalThreshold($this->cartConfigProvider);
    }

    public function testExecuteReturnsTrueWhenGrandTotalIsWithinThreshold(): void
    {
        $this->quote->method('getBaseGrandTotal')->willReturn(100.0);
        $this->assertTrue($this->checker->execute($this->quote));
    }

    public function testExecuteReturnsFalseWhenGrandTotalIsBelowThreshold(): void
    {
        $this->quote->method('getBaseGrandTotal')->willReturn(40.0);
        $this->assertFalse($this->checker->execute($this->quote));
    }

    public function testExecuteReturnsFalseWhenGrandTotalIsAboveThreshold(): void
    {
        $this->quote->method('getBaseGrandTotal')->willReturn(250.0);
        $this->assertFalse($this->checker->execute($this->quote));
    }
}
