<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Pragma\PragmaPayCore\Api\AcceptOrderPaymentInterface;
use Pragma\PragmaPayCore\Api\OrderPaymentResolverInterface;

class AcceptPayment implements AcceptOrderPaymentInterface
{
    public const PRAGMA_PAY_FINANCED_PAYMENT = 'pragma_pay_financed_payment';

    public function __construct(
        private readonly EventManager $eventManager,
        private readonly Transaction $transaction,
        private readonly OrderPaymentResolverInterface $orderPaymentResolver,
    ) {
    }

    /**
     * @throws CommandException
     * @throws LocalizedException
     */
    public function execute(string $paymentId, float $amount, string $orderIncrementUuid): void
    {
        $payment = $this->orderPaymentResolver->execute($paymentId, $orderIncrementUuid);
        if ($payment === null) {
            throw new CommandException(__('Payment does not exist'));
        }
        if (!$payment->canCapture()) {
            throw new CommandException(__('Payment could not be captured'));
        }
        $payment->capture();
        $order = $payment->getOrder();
        $eventData = ['order' => $order, 'payment' => $payment];
        $this->eventManager->dispatch(self::PRAGMA_PAY_FINANCED_PAYMENT, $eventData);
        $this->transaction->addObject($payment);
        $this->transaction->addObject($order);
        $this->transaction->save();
    }
}
