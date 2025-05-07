<?php
declare(strict_types=1);

namespace Pragma\PragmaPayAdminUi\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface;

class PragmaCartConfigProvider implements PragmaPayCartConfigProviderInterface
{
    private const MIN_ORDER_TOTAL = 'pragma_payment/cart/min_order_total';
    private const MAX_ORDER_TOTAL = 'pragma_payment/cart/max_order_total';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig)
    {
    }

    public function getMinOrderTotal(int $storeId): float
    {
        return (float)$this->scopeConfig->getValue(
            self::MIN_ORDER_TOTAL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getMaxOrderTotal(int $storeId): float
    {
        return (float)$this->scopeConfig->getValue(
            self::MAX_ORDER_TOTAL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
