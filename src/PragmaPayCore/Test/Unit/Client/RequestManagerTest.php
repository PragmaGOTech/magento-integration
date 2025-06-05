<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Client;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Client\RequestManager;

class RequestManagerTest extends TestCase
{
    private RequestManager $requestManager;

    protected function setUp(): void
    {
        // Arrange: Instantiate the RequestManager
        $this->requestManager = new RequestManager();
    }

    public function testFormatAmount(): void
    {
        // Arrange
        $amount = 123.45;

        // Act
        $result = $this->requestManager->formatAmount($amount);

        // Assert
        $this->assertSame(12345, $result);
    }

    public function testFormatPhoneWithValidNumber(): void
    {
        // Arrange
        $phone = '+48123456789';

        // Act
        $result = $this->requestManager->formatPhone($phone);

        // Assert
        $this->assertSame(['prefix' => '+48', 'number' => '123456789'], $result);
    }

    public function testFormatPhoneWithoutPlusPrefix(): void
    {
        // Arrange
        $phone = '123456789';

        // Act
        $result = $this->requestManager->formatPhone($phone);

        // Assert
        $this->assertSame(['prefix' => '+48', 'number' => '123456789'], $result);
    }

    public function testFormatRegistrationNumber(): void
    {
        // Arrange
        $countryCode = 'DE';
        $vatId = '123456789';

        // Act
        $result = $this->requestManager->formatRegistrationNumber($countryCode, $vatId);

        // Assert
        $this->assertSame(['registrationNumber' => 'DE123456789', 'country' => 'DE'], $result);
    }

    public function testFormatAmountWithZero(): void
    {
        $this->assertSame(0, $this->requestManager->formatAmount(0.0));
    }

    public function testFormatAmountWithNegativeValue(): void
    {
        $this->assertSame(-12345, $this->requestManager->formatAmount(-123.45));
    }

    public function testFormatAmountWithLargeValue(): void
    {
        $this->assertSame(123456789000, $this->requestManager->formatAmount(1234567890.00));
    }

    public function testFormatPhoneWithEmptyString(): void
    {
        $result = $this->requestManager->formatPhone('');
        $this->assertNull($result);
    }

    public function testFormatPhoneWithInvalidCharacters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->requestManager->formatPhone('+48abc123');
    }

    public function testFormatPhoneWithTooShortNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->requestManager->formatPhone('+48123');
    }

    public function testFormatPhoneWithTooLongNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->requestManager->formatPhone('+4812345678901236');
    }

    public function testFormatRegistrationNumberWithLowercaseCountryCode(): void
    {
        $result = $this->requestManager->formatRegistrationNumber('de', '123456789');
        $this->assertSame(['registrationNumber' => 'DE123456789', 'country' => 'DE'], $result);
    }
}
