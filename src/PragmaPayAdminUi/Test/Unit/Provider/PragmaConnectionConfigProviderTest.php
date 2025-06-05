<?php
declare(strict_types=1);

namespace Pragma\PragmaPayAdminUi\Test\Unit\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayAdminUi\Provider\PragmaConnectionConfigProvider;

class PragmaConnectionConfigProviderTest extends TestCase
{
    private ScopeConfigInterface $scopeConfig;
    private EncryptorInterface $encryptor;
    private PragmaConnectionConfigProvider $configProvider;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->encryptor = $this->createMock(EncryptorInterface::class);

        // Arrange: Instantiate the class with mocked dependencies
        $this->configProvider = new PragmaConnectionConfigProvider($this->scopeConfig, $this->encryptor);
    }

    public function testIsActive(): void
    {
        // Arrange
        $storeId = 1;
        $this->scopeConfig->method('isSetFlag')
            ->with('pragma_payment/general/is_active', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);

        // Act
        $result = $this->configProvider->isActive($storeId);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsSandbox(): void
    {
        // Arrange
        $storeId = 1;
        $this->scopeConfig->method('isSetFlag')
            ->with('pragma_payment/connection/is_sandbox', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(false);

        // Act
        $result = $this->configProvider->isSandbox($storeId);

        // Assert
        $this->assertFalse($result);
    }

    public function testGetApiUrl(): void
    {
        // Arrange
        $storeId = 1;
        $sandboxUrl = 'https://sandbox.example.com';
        $liveUrl = 'https://live.example.com';

        $this->scopeConfig->method('isSetFlag')
            ->with('pragma_payment/connection/is_sandbox', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(false);

        $this->scopeConfig->method('getValue')
            ->with('pragma_payment/connection/api_url', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($liveUrl);

        // Act
        $result = $this->configProvider->getApiUrl($storeId);

        // Assert
        $this->assertSame($liveUrl, $result);
    }

    public function testGetPartnerKey(): void
    {
        // Arrange
        $storeId = 1;
        $encryptedKey = 'encrypted_key';
        $decryptedKey = 'decrypted_key';

        $this->scopeConfig->method('isSetFlag')
            ->with('pragma_payment/connection/is_sandbox', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(false);

        $this->scopeConfig->method('getValue')
            ->with('pragma_payment/connection/partner_key', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($encryptedKey);

        $this->encryptor->method('decrypt')
            ->with($encryptedKey)
            ->willReturn($decryptedKey);

        // Act
        $result = $this->configProvider->getPartnerKey($storeId);

        // Assert
        $this->assertSame($decryptedKey, $result);
    }

    public function testGetReturnUrl(): void
    {
        // Arrange
        $storeId = 1;
        $expectedUrl = 'https://example.com/return';
        $this->scopeConfig->method('getValue')
            ->with('pragma_payment/connection/return_url', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedUrl);

        // Act
        $result = $this->configProvider->getReturnUrl($storeId);

        // Assert
        $this->assertSame($expectedUrl, $result);
    }

    public function testIsLogCartRequest(): void
    {
        // Arrange
        $storeId = 1;
        $this->scopeConfig->method('getValue')
            ->with('pragma_payment/connection/log_cart_request', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);

        // Act
        $result = $this->configProvider->isLogCartRequest($storeId);

        // Assert
        $this->assertTrue($result);
    }
}
