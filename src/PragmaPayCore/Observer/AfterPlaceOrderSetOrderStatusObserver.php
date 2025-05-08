<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class AfterPlaceOrderSetOrderStatusObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        /** @var Payment $payment */
        $payment = $observer->getData('payment');
        $method = $payment->getMethod();
        if ($method !== PragmaConnectionConfigProviderInterface::METHOD_NAME) {
            return;
        }
        $order = $payment->getOrder();
        $order->setState(Order::STATE_PENDING_PAYMENT)->setStatus(Order::STATE_PENDING_PAYMENT);
    }
}
