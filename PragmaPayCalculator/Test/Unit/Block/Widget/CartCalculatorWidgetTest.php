<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCalculator\Test\Unit\Block\Widget;

use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCalculator\Block\Widget\CartCalculatorWidget;
use Pragma\PragmaPayCalculator\Model\CalculatorApiConfig;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class CartCalculatorWidgetTest extends TestCase
{
    private CartCalculatorWidget $cartCalculatorWidget;
    private CalculatorApiConfig $calculatorApiConfig;
    private CheckoutSession $checkoutSession;
    private MockObject|Quote $quote;

    protected function setUp(): void
    {
        // Mock the Quote class and only the getGrandTotal method
        $this->quote = $this->getMockBuilder(Quote::class)
            ->addMethods(['getGrandTotal'])
            ->disableOriginalConstructor()
            ->getMock();

        // Arrange: Mock dependencies
        $context = $this->createMock(Context::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $connectionConfigProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $this->calculatorApiConfig = $this->createMock(CalculatorApiConfig::class);
        $this->checkoutSession = $this->createMock(CheckoutSession::class);

        // Arrange: Instantiate the CartCalculatorWidget with mocked dependencies
        $this->cartCalculatorWidget = new CartCalculatorWidget(
            $context,
            $this->calculatorApiConfig,
            $storeManager,
            $connectionConfigProvider,
            $this->checkoutSession
        );
    }

    public function testGetCartTotalWithValidAmount(): void
    {
        // Arrange: Mock the quote and grand total
        $grandTotal = 150.0;
        $formattedAmount = 15000;

        $this->quote->method('getGrandTotal')->willReturn($grandTotal);

        $this->checkoutSession->method('getQuote')->willReturn($this->quote);
        $this->calculatorApiConfig->method('prepareAmount')->with($grandTotal)->willReturn($formattedAmount);

        // Act: Call the getCartTotal method
        $result = $this->cartCalculatorWidget->getCartTotal();

        // Assert: Verify the result
        $this->assertSame($formattedAmount, $result);
    }

    public function testGetCartTotalWithInvalidAmount(): void
    {
        // Arrange: Mock the quote to return an invalid grand total
        $this->quote->method('getGrandTotal')->willReturn(null);

        $this->checkoutSession->method('getQuote')->willReturn($this->quote);

        // Act: Call the getCartTotal method
        $result = $this->cartCalculatorWidget->getCartTotal();

        // Assert: Verify the result is null
        $this->assertNull($result);
    }

    public function testGetCartTotalHandlesException(): void
    {
        // Arrange: Mock the quote to throw an exception
        $this->checkoutSession->method('getQuote')->willThrowException(new Exception());

        // Act: Call the getCartTotal method
        $result = $this->cartCalculatorWidget->getCartTotal();

        // Assert: Verify the result is null
        $this->assertNull($result);
    }
}
