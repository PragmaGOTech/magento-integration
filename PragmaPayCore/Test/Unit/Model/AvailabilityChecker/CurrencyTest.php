<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Model\AvailabilityChecker;

use Magento\Quote\Model\Quote;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\Currency;

class CurrencyTest extends TestCase
{
    private $quote;
    private $store;
    private $checker;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->store = $this->createMock(Store::class);
        $this->quote = $this->createMock(Quote::class);
        $this->quote->method('getStore')->willReturn($this->store);

        // Arrange: Instantiate the Currency checker
        $this->checker = new Currency();
    }

    public function testExecuteReturnsTrueForAllowedCurrency(): void
    {
        // Arrange
        $this->store->method('getCurrentCurrencyCode')->willReturn('PLN');

        // Act
        $result = $this->checker->execute($this->quote);

        // Assert
        $this->assertTrue($result);
    }

    public function testExecuteReturnsFalseForDisallowedCurrency(): void
    {
        // Arrange
        $this->store->method('getCurrentCurrencyCode')->willReturn('USD');

        // Act
        $result = $this->checker->execute($this->quote);

        // Assert
        $this->assertFalse($result);
    }
}
