<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Request;

use InvalidArgumentException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Pragma\PragmaPayCore\Api\TransactionHistoryProviderInterface as TransactionHistory;
use Pragma\PragmaPayCore\Client\RequestManager;

class TransactionDataBuilder implements BuilderInterface
{
    public function __construct(
        private readonly TransactionHistory $orderHistoryProvider,
        private readonly RequestManager $requestManager,
    ) {
    }

    public function build(array $buildSubject): array
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new InvalidArgumentException('Payment data object should be provided');
        }
        $buildPayment = $buildSubject['payment'];
        $order = $buildPayment->getOrder();
        $customerEmail = $order->getBillingAddress()->getEmail();
        $preparedTransactionData = $this->prepareTransactionData($customerEmail, (int)$order->getStoreId());

        if (!$preparedTransactionData) {
            return [];
        }

        return [
            'body' => [
                'transactionData' => [
                    'identifier' => [
                        'email' => $customerEmail,
                    ],
                    'data' => [
                        'monthlyReports' => $preparedTransactionData,
                    ]
                ],
            ],
        ];
    }

    private function prepareTransactionData(string $customerEmail, int $storeId): ?array
    {
        $preparedTransactionData = [];

        // Fetch existing transaction data
        $existingData = $this->orderHistoryProvider->execute($customerEmail, $storeId);
        if (!$existingData) {
            return null;
        }

        // Define current month and start from 11 months ago to current month
        $currentDate = new \DateTime('first day of this month');
        $startDate = (clone $currentDate)->modify('-11 months');

        for ($date = clone $startDate; $date <= $currentDate; $date->modify('+1 month')) {
            $monthKey = $date->format('Y-m-01');

            $transactionsInfo = $existingData[$monthKey] ?? [];

            $preparedTransactionData[] = [
                'month' => $monthKey,
                "transactionsValue" => (int)$this->requestManager->formatAmount((float)($transactionsInfo[TransactionHistory::TOTAL_AMOUNT] ?? 0)),
                "transactionsCount" => $transactionsInfo[TransactionHistory::ORDER_COUNT] ?? 0,
                "refundsValue" => (int)$this->requestManager->formatAmount((float)($transactionsInfo[TransactionHistory::TOTAL_REFUNDED] ?? 0)),
                "refundsNumber" => $transactionsInfo[TransactionHistory::REFUND_COUNT] ?? 0,
            ];
        }

        return $preparedTransactionData;
    }
}
