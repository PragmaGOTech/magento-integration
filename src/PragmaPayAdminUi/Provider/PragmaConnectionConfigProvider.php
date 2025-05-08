<?php
declare(strict_types=1);

namespace Pragma\PragmaPayAdminUi\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class PragmaConnectionConfigProvider implements PragmaConnectionConfigProviderInterface
{
    private const IS_ACTIVE = 'pragma_payment/general/is_active';
    private const IS_SANDBOX = 'pragma_payment/connection/is_sandbox';
    private const API_URL = 'pragma_payment/connection/api_url';
    private const SANDBOX_API_URL = 'pragma_payment/connection/sandbox_api_url';
    private const PARTNER_KEY = 'pragma_payment/connection/partner_key';
    private const SANDBOX_PARTNER_KEY = 'pragma_payment/connection/sandbox_partner_key';
    private const PARTNER_SECRET = 'pragma_payment/connection/partner_secret';
    private const SANDBOX_PARTNER_SECRET = 'pragma_payment/connection/sandbox_partner_secret';
    private const RETURN_URL = 'pragma_payment/connection/return_url';
    private const CANCEL_URL = 'pragma_payment/connection/cancel_url';
    private const NOTIFICATION_URL = 'pragma_payment/connection/notification_url';
    private const LOG_CART_REQUEST = 'pragma_payment/connection/log_cart_request';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {
    }

    public function isActive(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(self::IS_ACTIVE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isSandbox(int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(self::IS_SANDBOX, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getApiUrl(int $storeId): string
    {
        if ($this->isSandbox($storeId)) {
            return (string)$this->scopeConfig->getValue(self::SANDBOX_API_URL, ScopeInterface::SCOPE_STORE, $storeId);
        }
        return (string)$this->scopeConfig->getValue(self::API_URL, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPartnerKey(int $storeId): string
    {
        if ($this->isSandbox($storeId)) {
            $encryptedValue = (string)$this->scopeConfig->getValue(self::SANDBOX_PARTNER_KEY, ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $encryptedValue = (string)$this->scopeConfig->getValue(self::PARTNER_KEY, ScopeInterface::SCOPE_STORE, $storeId);
        }
        return $this->encryptor->decrypt($encryptedValue);
    }

    public function getPartnerSecret(int $storeId): string
    {
        if ($this->isSandbox($storeId)) {
            $encryptedValue = (string)$this->scopeConfig->getValue(self::SANDBOX_PARTNER_SECRET, ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $encryptedValue = (string)$this->scopeConfig->getValue(self::PARTNER_SECRET, ScopeInterface::SCOPE_STORE, $storeId);
        }
        return $this->encryptor->decrypt($encryptedValue);
    }

    public function getReturnUrl(int $storeId): string
    {
        return (string)$this->scopeConfig->getValue(
            self::RETURN_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCancelUrl(int $storeId): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CANCEL_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getNotificationUrl(int $storeId): string
    {
        return (string)$this->scopeConfig->getValue(
            self::NOTIFICATION_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isLogCartRequest(int $storeId): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::LOG_CART_REQUEST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
