<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCalculator\Test\Unit\Block\Widget;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCalculator\Block\Widget\ProductCalculatorWidget;
use Pragma\PragmaPayCalculator\Model\CalculatorApiConfig;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class ProductCalculatorWidgetTest extends TestCase
{
    private ProductCalculatorWidget $productCalculatorWidget;
    private ProductRepositoryInterface $productRepository;
    private CalculatorApiConfig $calculatorApiConfig;
    private Registry $registry;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $context = $this->createMock(Context::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $connectionConfigProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->calculatorApiConfig = $this->createMock(CalculatorApiConfig::class);
        $this->registry = $this->createMock(Registry::class);

        // Instantiate the ProductCalculatorWidget with mocked dependencies
        $this->productCalculatorWidget = new ProductCalculatorWidget(
            $context,
            $this->productRepository,
            $this->calculatorApiConfig,
            $storeManager,
            $connectionConfigProvider,
            $this->registry
        );
    }

    public function testGetProductPriceWithValidProduct(): void
    {
        // Arrange
        $finalPrice = 200.0;
        $formattedPrice = 20000;

        $product = $this->createMock(Product::class);
        $product->method('getFinalPrice')->willReturn($finalPrice);

        $this->productRepository->method('getById')->willReturn($product);
        $this->calculatorApiConfig->method('prepareAmount')->with($finalPrice)->willReturn($formattedPrice);

        $this->productCalculatorWidget->setData('product_id', 1);

        // Act
        $result = $this->productCalculatorWidget->getProductPrice();

        // Assert
        $this->assertSame($formattedPrice, $result);
    }

    public function testGetProductPriceWithNoProduct(): void
    {
        // Arrange
        $this->registry->method('registry')->with('current_product')->willReturn(null);

        // Act
        $result = $this->productCalculatorWidget->getProductPrice();

        // Assert
        $this->assertNull($result);
    }

    public function testGetProductPriceHandlesException(): void
    {
        // Arrange
        $this->productRepository->method('getById')->willThrowException(new NoSuchEntityException());

        $this->productCalculatorWidget->setData('product_id', 1);

        // Act
        $result = $this->productCalculatorWidget->getProductPrice();

        // Assert
        $this->assertNull($result);
    }
}
