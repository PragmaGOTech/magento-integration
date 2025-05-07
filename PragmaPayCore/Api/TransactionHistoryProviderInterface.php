<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

interface TransactionHistoryProviderInterface
{
    public const TOTAL_AMOUNT = 'total_amount';
    public const TOTAL_REFUNDED = 'total_refunded';
    public const ORDER_COUNT = 'order_count';
    public const REFUND_COUNT = 'refund_count';

    public function execute(string $customerEmail, int $storeId): array;
}
