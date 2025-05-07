<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Service;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Api\OrderPaymentResolverInterface;
use Pragma\PragmaPayCore\Service\AcceptPayment;

class AcceptPaymentTest extends TestCase
{
    private AcceptPayment $acceptPayment;
    private MockObject $eventManager;
    private MockObject $transaction;
    private MockObject $orderPaymentResolver;

    protected function setUp(): void
    {
        $this->eventManager = $this->createMock(EventManager::class);
        $this->transaction = $this->createMock(Transaction::class);
        $this->orderPaymentResolver = $this->createMock(OrderPaymentResolverInterface::class);

        $this->acceptPayment = new AcceptPayment(
            $this->eventManager,
            $this->transaction,
            $this->orderPaymentResolver
        );
    }

    public function testExecuteSuccessfullyCapturesPayment(): void
    {
        $paymentId = '12345';
        $amount = 100.0;
        $orderIncrementId = '100000001';

        $payment = $this->createMock(Payment::class);
        $order = $this->createMock(Order::class);

        $payment->method('canCapture')->willReturn(true);
        $payment->expects($this->once())->method('capture');
        $payment->method('getOrder')->willReturn($order);

        $this->orderPaymentResolver->method('execute')->with($paymentId, $orderIncrementId)->willReturn($payment);

        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->with(AcceptPayment::PRAGMA_PAY_FINANCED_PAYMENT, ['order' => $order, 'payment' => $payment]);

        $addObjectCallCount = 0;
        $this->transaction->method('addObject')
            ->willReturnCallback(function ($object) use (&$addObjectCallCount, $payment, $order) {
                match ($addObjectCallCount++) {
                    0 => $this->assertSame($payment, $object),
                    1 => $this->assertSame($order, $object),
                };
                return $this->transaction;
            });

        $this->transaction->expects($this->once())->method('save');

        $this->acceptPayment->execute($paymentId, $amount, $orderIncrementId);
    }

    public function testExecuteThrowsExceptionWhenPaymentDoesNotExist(): void
    {
        $paymentId = '12345';
        $amount = 100.0;
        $orderIncrementId = '100000001';

        $this->orderPaymentResolver->method('execute')->with($paymentId, $orderIncrementId)->willReturn(null);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Payment does not exist');

        $this->acceptPayment->execute($paymentId, $amount, $orderIncrementId);
    }

    public function testExecuteThrowsExceptionWhenPaymentCannotBeCaptured(): void
    {
        $paymentId = '12345';
        $amount = 100.0;
        $orderIncrementId = '100000001';

        $payment = $this->createMock(Payment::class);
        $payment->method('canCapture')->willReturn(false);

        $this->orderPaymentResolver->method('execute')->with($paymentId, $orderIncrementId)->willReturn($payment);

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('Payment could not be captured');

        $this->acceptPayment->execute($paymentId, $amount, $orderIncrementId);
    }
}
