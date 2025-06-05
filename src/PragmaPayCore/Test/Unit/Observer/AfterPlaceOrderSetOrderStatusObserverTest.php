<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pragma\PragmaPayCore\Observer\AfterPlaceOrderSetOrderStatusObserver;

class AfterPlaceOrderSetOrderStatusObserverTest extends TestCase
{
    /**
     * @var MockObject|Observer
     */
    private $observer;

    /**
     * @var MockObject|Payment
     */
    private $payment;

    /**
     * @var MockObject|Order
     */
    private $order;

    /**
     * @var AfterPlaceOrderSetOrderStatusObserver
     */
    private $afterPlaceOrderSetOrderStatusObserver;

    protected function setUp(): void
    {
        $this->observer = $this->createMock(Observer::class);
        $this->payment = $this->createMock(Payment::class);
        $this->order = $this->createMock(Order::class);
        $this->payment->expects($this->atMost(1))->method('getOrder')->willReturn($this->order);
        $this->observer->expects($this->once())->method('getData')->with('payment')->willReturn($this->payment);
        $this->afterPlaceOrderSetOrderStatusObserver = new AfterPlaceOrderSetOrderStatusObserver();
    }

    public function testExecute(): void
    {
        $this->payment->expects($this->once())->method('getMethod')->willReturn('pragma_payment');
        $this->order->expects($this->once())->method('setState')->willReturn($this->order);
        $this->order->expects($this->once())->method('setStatus')->willReturn($this->order);
        $this->afterPlaceOrderSetOrderStatusObserver->execute($this->observer);
    }

    public function testExecuteWithoutSpingoPayment(): void
    {
        $this->payment->expects($this->once())->method('getMethod')->willReturn('test_method');
        $this->order->expects($this->never())->method('setState')->willReturn($this->order);
        $this->order->expects($this->never())->method('setStatus')->willReturn($this->order);
        $this->afterPlaceOrderSetOrderStatusObserver->execute($this->observer);
    }
}
