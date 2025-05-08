<?php
declare(strict_types=1);

namespace Pragma\PragmaPayFrontendUi\Test\Unit\Provider;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayFrontendUi\Provider\PragmaPayCheckoutConfigProvider;

class PragmaPayCheckoutConfigProviderTest extends TestCase
{
    private PragmaPayCheckoutConfigProvider $configProvider;
    private PragmaConnectionConfigProviderInterface $connectionConfigProvider;
    private StoreManagerInterface $storeManager;
    private Repository $assetRepository;
    private ResolverInterface $resolver;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->connectionConfigProvider = $this->createMock(PragmaConnectionConfigProviderInterface::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->assetRepository = $this->createMock(Repository::class);
        $this->resolver = $this->createMock(ResolverInterface::class);

        // Arrange: Instantiate the PragmaPayCheckoutConfigProvider
        $this->configProvider = new PragmaPayCheckoutConfigProvider(
            $this->connectionConfigProvider,
            $this->storeManager,
            $this->assetRepository,
            $this->resolver
        );
    }

    public function testGetConfigWithActiveStore(): void
    {
        // Arrange
        $storeId = 1;
        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn($storeId);

        $this->storeManager->method('getStore')->willReturn($store);
        $this->connectionConfigProvider->method('isActive')->with($storeId)->willReturn(true);
        $this->assetRepository->method('getUrl')->willReturnCallback(function (string $route) {
            return match ($route) {
                'Pragma_PragmaPayFrontendUi::images/logo.png' => 'https://example.com/logo.png',
                'Pragma_PragmaPayFrontendUi::images/logo-dark.png' => 'https://example.com/logo-dark.png',
                default => null,
            };
        });
        $this->resolver->method('getLocale')->willReturn('en_US');

        // Act
        $config = $this->configProvider->getConfig();

        // Assert
        $this->assertArrayHasKey('payment', $config);
        $this->assertTrue($config['payment']['pragmaPayment']['isActive']);
        $this->assertSame('https://example.com/logo.png', $config['payment']['pragmaPayment']['logoSrc']);
        $this->assertSame('https://example.com/logo-dark.png', $config['payment']['pragmaPayment']['logoDarkSrc']);
        $this->assertSame('en', $config['payment']['pragmaPayment']['language']);
    }

    public function testGetConfigWithInactiveStore(): void
    {
        // Arrange
        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);

        $this->storeManager->method('getStore')->willReturn($store);
        $this->connectionConfigProvider->method('isActive')->willThrowException(new NoSuchEntityException());
        $this->assetRepository->method('getUrl')->willReturn('https://example.com/logo.png');
        $this->resolver->method('getLocale')->willReturn('en_US');

        // Act
        $config = $this->configProvider->getConfig();

        // Assert
        $this->assertArrayHasKey('payment', $config);
        $this->assertFalse($config['payment']['pragmaPayment']['isActive']);
    }
}
