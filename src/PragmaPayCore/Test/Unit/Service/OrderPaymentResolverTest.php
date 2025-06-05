<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Service;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Service\GenerateUuid5;
use Pragma\PragmaPayCore\Service\OrderPaymentResolver;

class OrderPaymentResolverTest extends TestCase
{
    private OrderPaymentResolver $orderPaymentResolver;
    private MockObject $searchCriteriaBuilder;
    private MockObject $transactionRepository;
    private MockObject $paymentRepository;
    private GenerateUuid5 $generateUuid5;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $this->paymentRepository = $this->createMock(OrderPaymentRepositoryInterface::class);
        $this->generateUuid5 = new GenerateUuid5();

        // Arrange: Instantiate the OrderPaymentResolver
        $this->orderPaymentResolver = new OrderPaymentResolver(
            $this->searchCriteriaBuilder,
            $this->transactionRepository,
            $this->paymentRepository,
            $this->generateUuid5
        );
    }

    public function testExecuteReturnsPaymentWhenTransactionExists(): void
    {
        // Arrange
        $pragmaPaymentId = 'txn123';
        $orderIncrementId = '100000001';

        $transaction = $this->createMock(TransactionInterface::class);
        $payment = $this->createMock(Payment::class);
        $order = $this->createMock(Order::class);

        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilder->method('addFilter')->with('txn_id', $pragmaPaymentId)->willReturnSelf();
        $this->searchCriteriaBuilder->method('create')->willReturn($searchCriteria);

        // Mock the SearchResultsInterface
        $searchResults = $this->createMock(\Magento\Framework\Api\SearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([$transaction]);

        $this->transactionRepository->method('getList')->with($searchCriteria)->willReturn($searchResults);

        $transaction->method('getPaymentId')->willReturn(1);
        $transaction->method('getIsClosed')->willReturn(false);

        $this->paymentRepository->method('get')->with(1)->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getIncrementId')->willReturn($orderIncrementId);

        $payment->expects($this->once())->method('setData')->with('is_active', true);
        $order->expects($this->once())->method('setData')->with(OrderInterface::PAYMENT, $payment);

        // Act
        $result = $this->orderPaymentResolver->execute($pragmaPaymentId, $this->generateUuid5->execute($orderIncrementId));

        // Assert
        $this->assertSame($payment, $result);
    }

    public function testExecuteReturnsNullWhenTransactionDoesNotExist(): void
    {
        // Arrange
        $pragmaPaymentId = 'txn123';
        $orderIncrementId = '100000001';

        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilder->method('addFilter')->with('txn_id', $pragmaPaymentId)->willReturnSelf();
        $this->searchCriteriaBuilder->method('create')->willReturn($searchCriteria);

        // Mock the SearchResultsInterface
        $searchResults = $this->createMock(\Magento\Framework\Api\SearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([]);

        $this->transactionRepository->method('getList')->with($searchCriteria)->willReturn($searchResults);


        // Act
        $result = $this->orderPaymentResolver->execute($pragmaPaymentId, $this->generateUuid5->execute($orderIncrementId));

        // Assert
        $this->assertNull($result);
    }

    public function testExecuteReturnsNullWhenOrderIncrementIdDoesNotMatch(): void
    {
        // Arrange
        $pragmaPaymentId = 'txn123';
        $orderIncrementId = '100000001';

        $transaction = $this->createMock(TransactionInterface::class);
        $payment = $this->createMock(Payment::class);
        $order = $this->createMock(OrderInterface::class);

        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilder->method('addFilter')->with('txn_id', $pragmaPaymentId)->willReturnSelf();
        $this->searchCriteriaBuilder->method('create')->willReturn($searchCriteria);

        // Mock the SearchResultsInterface
        $searchResults = $this->createMock(\Magento\Framework\Api\SearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([$transaction]);

        $this->transactionRepository->method('getList')->with($searchCriteria)->willReturn($searchResults);

        $transaction->method('getPaymentId')->willReturn(1);

        $this->paymentRepository->method('get')->with(1)->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getIncrementId')->willReturn('100000002'); // Mismatched ID

        // Act
        $result = $this->orderPaymentResolver->execute($pragmaPaymentId, $this->generateUuid5->execute($orderIncrementId));

        // Assert
        $this->assertNull($result);
    }
}
