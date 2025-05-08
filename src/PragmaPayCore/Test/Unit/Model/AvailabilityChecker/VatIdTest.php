<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Model\AvailabilityChecker;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Model\AvailabilityChecker\VatId;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;

class VatIdTest extends TestCase
{
    public function testExecuteReturnsTrueWhenVatIdIsPresent()
    {
        $vatIdChecker = new VatId();

        $billingAddress = $this->createMock(Address::class);
        $billingAddress->method('getVatId')->willReturn('DE123456789');

        $quote = $this->createMock(Quote::class);
        $quote->method('getBillingAddress')->willReturn($billingAddress);

        $this->assertTrue($vatIdChecker->execute($quote));
    }

    public function testExecuteReturnsFalseWhenVatIdIsNotPresent()
    {
        $vatIdChecker = new VatId();

        $billingAddress = $this->createMock(Address::class);
        $billingAddress->method('getVatId')->willReturn('');

        $quote = $this->createMock(Quote::class);
        $quote->method('getBillingAddress')->willReturn($billingAddress);

        $this->assertFalse($vatIdChecker->execute($quote));
    }
}
