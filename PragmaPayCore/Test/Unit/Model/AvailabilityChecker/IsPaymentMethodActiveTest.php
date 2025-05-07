<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Model\AvailabilityChecker;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\IsPaymentMethodActive;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Magento\Quote\Model\Quote;

class IsPaymentMethodActiveTest extends TestCase
{
    public function testExecuteReturnsTrueWhenPaymentMethodIsActive()
    {
        $configProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $configProvider->method('isActive')->willReturn(true);

        $quote = $this->createMock(Quote::class);
        $quote->method('getStoreId')->willReturn(1);

        $checker = new IsPaymentMethodActive($configProvider);

        $this->assertTrue($checker->execute($quote));
    }

    public function testExecuteReturnsFalseWhenPaymentMethodIsNotActive()
    {
        $configProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $configProvider->method('isActive')->willReturn(false);

        $quote = $this->createMock(Quote::class);
        $quote->method('getStoreId')->willReturn(1);

        $checker = new IsPaymentMethodActive($configProvider);

        $this->assertFalse($checker->execute($quote));
    }
}
