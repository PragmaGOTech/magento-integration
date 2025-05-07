<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Pragma\PragmaPayCore\Api\TransactionHistoryProviderInterface;

class CustomerTransactionHistoryProvider implements TransactionHistoryProviderInterface
{
    private const MONTH = 'month';

    public function __construct(
        private readonly CollectionFactory $orderCollectionFactory,
        private readonly TimezoneInterface $timezone
    ) {
    }

    public function execute(string $customerEmail, int $storeId): array
    {
        return $this->getOrders($customerEmail, $storeId);
    }

    private function getOrders(string $customerEmail, int $storeId): array {
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToSelect(['entity_id', 'created_at', 'grand_total', 'total_refunded'])
            ->addFieldToFilter('customer_email', ['eq' => $customerEmail])
            ->addFieldToFilter('store_id', ['eq' => $storeId])
            ->setOrder('created_at', 'DESC');

        $ordersByMonth = [];

        foreach ($orderCollection as $order) {
            // Convert to store timezone
            $orderDate = $this->timezone->date(new DateTime($order->getCreatedAt()));
            $monthKey = $orderDate->format('Y-m-01');

            if (!isset($ordersByMonth[$monthKey])) {
                $ordersByMonth[$monthKey] = [
                    self::MONTH => $monthKey,
                    self::TOTAL_AMOUNT => 0,
                    self::ORDER_COUNT => 0,
                    self::REFUND_COUNT => 0,
                    self::TOTAL_REFUNDED => 0,
                ];
            }

            if ($order->getTotalRefunded() > 0) {
                $ordersByMonth[$monthKey][self::REFUND_COUNT]++;
                $ordersByMonth[$monthKey][self::TOTAL_REFUNDED] += $order->getTotalRefunded();
            }
            $ordersByMonth[$monthKey][self::ORDER_COUNT]++;
            $ordersByMonth[$monthKey][self::TOTAL_AMOUNT] += $order->getGrandTotal();
        }

        return $ordersByMonth;
    }
}
