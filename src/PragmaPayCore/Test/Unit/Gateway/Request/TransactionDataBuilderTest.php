<?php

declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\TransactionHistoryProviderInterface as TransactionHistory;
use Pragma\PragmaPayCore\Client\RequestManager;
use Pragma\PragmaPayCore\Gateway\Request\TransactionDataBuilder;

class TransactionDataBuilderTest extends TestCase
{
    private TransactionHistory $orderHistoryProvider;
    private RequestManager $requestManager;
    private TransactionDataBuilder $transactionDataBuilder;

    protected function setUp(): void
    {
        $this->orderHistoryProvider = $this->createMock(TransactionHistory::class);
        $this->requestManager = $this->createMock(RequestManager::class);

        $this->transactionDataBuilder = new TransactionDataBuilder(
            $this->orderHistoryProvider,
            $this->requestManager
        );
    }

    public function testBuildWithValidData(): void
    {
        // Arrange
        $email = 'customer@example.com';
        $storeId = 1;

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $order = $this->createMock(OrderAdapterInterface::class);
        $billingAddress = $this->createMock(OrderAddressInterface::class);

        $paymentDataObject->method('getOrder')->willReturn($order);
        $order->method('getBillingAddress')->willReturn($billingAddress);
        $billingAddress->method('getEmail')->willReturn($email);
        $order->method('getStoreId')->willReturn($storeId);

        $existingData = [
            (new \DateTime('first day of this month'))->format('Y-m-01') => [
                TransactionHistory::TOTAL_AMOUNT => 500,
                TransactionHistory::ORDER_COUNT => 2,
                TransactionHistory::TOTAL_REFUNDED => 50,
                TransactionHistory::REFUND_COUNT => 1
            ]
        ];

        $this->orderHistoryProvider->method('execute')->with($email, $storeId)->willReturn($existingData);

        $this->requestManager->method('formatAmount')->willReturnCallback(fn($amount) => (int)$amount * 100);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->transactionDataBuilder->build($buildSubject);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('transactionData', $result['body']);
        $this->assertArrayHasKey('monthlyReports', $result['body']['transactionData']['data']);
        $this->assertCount(12, $result['body']['transactionData']['data']['monthlyReports']);
    }

    public function testBuildThrowsExceptionIfPaymentNotProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment data object should be provided');

        $this->transactionDataBuilder->build([]);
    }

    public function testBuildReturnsEmptyIfNoTransactionData(): void
    {
        // Arrange
        $email = 'customer@example.com';
        $storeId = 1;

        $paymentDataObject = $this->createMock(PaymentDataObjectInterface::class);
        $order = $this->createMock(OrderAdapterInterface::class);
        $billingAddress = $this->createMock(OrderAddressInterface::class);

        $paymentDataObject->method('getOrder')->willReturn($order);
        $order->method('getBillingAddress')->willReturn($billingAddress);
        $billingAddress->method('getEmail')->willReturn($email);
        $order->method('getStoreId')->willReturn($storeId);

        $this->orderHistoryProvider->method('execute')->with($email, $storeId)->willReturn([]);

        $buildSubject = ['payment' => $paymentDataObject];

        // Act
        $result = $this->transactionDataBuilder->build($buildSubject);

        // Assert
        $this->assertSame([], $result);
    }
}
