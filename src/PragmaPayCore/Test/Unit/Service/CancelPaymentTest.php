<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Service;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\Transaction as DbTransaction;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\OrderPaymentResolverInterface;
use Pragma\PragmaPayCore\Service\CancelPayment;

class CancelPaymentTest extends TestCase
{
    private CancelPayment $cancelPayment;
    private MockObject $eventManager;
    private MockObject $transaction;
    private MockObject $orderPaymentResolver;
    private MockObject $searchCriteriaBuilder;
    private MockObject $transactionRepository;
    private MockObject $orderRepository;

    protected function setUp(): void
    {
        // Arrange: Mock dependencies
        $this->eventManager = $this->createMock(EventManager::class);
        $this->transaction = $this->createMock(DbTransaction::class);
        $this->orderPaymentResolver = $this->createMock(OrderPaymentResolverInterface::class);
        $this->searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $this->transactionRepository = $this->createMock(TransactionRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);

        // Arrange: Instantiate the CancelPayment class
        $this->cancelPayment = new CancelPayment(
            $this->eventManager,
            $this->transaction,
            $this->orderPaymentResolver,
            $this->searchCriteriaBuilder,
            $this->transactionRepository,
            $this->orderRepository
        );
    }

    public function testExecuteSuccessfullyCancelsOrder(): void
    {
        // Arrange
        $paymentId = '12345';
        $orderIncrementId = '100000001';
        $orderId = '1';
        $paymentId = '1';

        $payment = $this->createMock(Payment::class);
        $order = $this->createMock(Order::class);
        $transaction = $this->createMock(Transaction::class);

        $payment->method('getOrder')->willReturn($order);
        $payment->method('getEntityId')->willReturn($paymentId);
        $order->method('canCancel')->willReturn(true);
        $order->method('getEntityId')->willReturn($orderId);
        $order->expects($this->once())->method('cancel');
        $this->orderPaymentResolver->method('execute')->with($paymentId, $orderIncrementId)->willReturn($payment);

        $this->searchCriteriaBuilder->method('addFilter')->willReturnSelf();
        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilder->method('create')->willReturn($searchCriteria);

        // Mock the SearchResultsInterface
        $searchResults = $this->createMock(\Magento\Framework\Api\SearchResultsInterface::class);
        $searchResults->method('getItems')->willReturn([$transaction]);

        $this->transactionRepository->method('getList')->with($searchCriteria)->willReturn($searchResults);
        $transaction->expects($this->once())->method('setIsClosed')->with(1);
        $this->transaction->expects($this->once())->method('addObject')->with($transaction);
        $this->transaction->expects($this->once())->method('save');

        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->with(CancelPayment::PRAGMA_PAY_CANCELED_PAYMENT, ['order' => $order, 'payment' => $payment]);

        // Act
        $this->cancelPayment->execute($paymentId, $orderIncrementId);
    }

    public function testExecuteThrowsExceptionWhenPaymentDoesNotExist(): void
    {
        // Arrange
        $paymentId = '12345';
        $orderIncrementId = '100000001';

        $this->orderPaymentResolver->method('execute')->with($paymentId, $orderIncrementId)->willReturn(null);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Payment does not exist');

        // Act
        $this->cancelPayment->execute($paymentId, $orderIncrementId);
    }

    public function testExecuteDoesNotCancelOrderWhenCannotCancel(): void
    {
        // Arrange
        $paymentId = '12345';
        $orderIncrementId = '100000001';

        $payment = $this->createMock(Payment::class);
        $order = $this->createMock(Order::class);

        $payment->method('getOrder')->willReturn($order);
        $order->method('canCancel')->willReturn(false);
        $this->orderPaymentResolver->method('execute')->with($paymentId, $orderIncrementId)->willReturn($payment);

        $this->transaction->expects($this->never())->method('save');
        $this->eventManager->expects($this->never())->method('dispatch');

        // Act
        $this->cancelPayment->execute($paymentId, $orderIncrementId);
    }
}
