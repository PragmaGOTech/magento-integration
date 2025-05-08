<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCalculator\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCalculator\Model\CalculatorApiConfig;
use Pragma\PragmaPayCore\Client\RequestManager;

class CalculatorApiConfigTest extends TestCase
{
    private CalculatorApiConfig $calculatorApiConfig;
    private RequestManager $requestManager;

    protected function setUp(): void
    {
        // Arrange: Mock the RequestManager dependency
        $this->requestManager = $this->createMock(RequestManager::class);

        // Arrange: Instantiate the CalculatorApiConfig with the mocked dependency
        $this->calculatorApiConfig = new CalculatorApiConfig($this->requestManager);
    }

    public function testPrepareAmountWithValidAmount(): void
    {
        // Arrange: Mock a valid amount and its formatted result
        $amount = 200.0;
        $formattedAmount = 20000;

        $this->requestManager->method('formatAmount')->with($amount)->willReturn($formattedAmount);

        // Act: Call the prepareAmount method
        $result = $this->calculatorApiConfig->prepareAmount($amount);

        // Assert: Verify the result
        $this->assertSame($formattedAmount, $result);
    }

    public function testPrepareAmountWithNullAmount(): void
    {
        // Act: Call the prepareAmount method with null
        $result = $this->calculatorApiConfig->prepareAmount(null);

        // Assert: Verify the result is null
        $this->assertNull($result);
    }

    public function testPrepareAmountWithAmountBelowMinimum(): void
    {
        // Act: Call the prepareAmount method with an amount below the minimum
        $result = $this->calculatorApiConfig->prepareAmount(50.0);

        // Assert: Verify the result is null
        $this->assertNull($result);
    }
}
