<?php
declare(strict_types=1);

namespace Pragma\PragmaPayAdminUi\Test\Unit\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayAdminUi\Provider\PragmaCartConfigProvider;

class PragmaCartConfigProviderTest extends TestCase
{
    private ScopeConfigInterface $scopeConfig;
    private PragmaCartConfigProvider $cartConfigProvider;

    protected function setUp(): void
    {
        // Arrange: Mock the ScopeConfigInterface
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        // Arrange: Instantiate the PragmaCartConfigProvider with the mocked dependency
        $this->cartConfigProvider = new PragmaCartConfigProvider($this->scopeConfig);
    }

    public function testGetMinOrderTotal(): void
    {
        // Arrange: Set up the mock to return a specific value
        $storeId = 1;
        $expectedMinOrderTotal = 50.0;
        $this->scopeConfig->method('getValue')
            ->with('pragma_payment/cart/min_order_total', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn((string)$expectedMinOrderTotal);

        // Act: Call the method
        $result = $this->cartConfigProvider->getMinOrderTotal($storeId);

        // Assert: Verify the result
        $this->assertSame($expectedMinOrderTotal, $result);
    }

    public function testGetMaxOrderTotal(): void
    {
        // Arrange: Set up the mock to return a specific value
        $storeId = 1;
        $expectedMaxOrderTotal = 1000.0;
        $this->scopeConfig->method('getValue')
            ->with('pragma_payment/cart/max_order_total', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn((string)$expectedMaxOrderTotal);

        // Act: Call the method
        $result = $this->cartConfigProvider->getMaxOrderTotal($storeId);

        // Assert: Verify the result
        $this->assertSame($expectedMaxOrderTotal, $result);
    }
}
