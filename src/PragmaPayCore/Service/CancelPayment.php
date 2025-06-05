<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction as TransactionModel;
use Pragma\PragmaPayCore\Api\CancelOrderPaymentInterface;
use Pragma\PragmaPayCore\Api\OrderPaymentResolverInterface;

class CancelPayment implements CancelOrderPaymentInterface
{
    public const PRAGMA_PAY_CANCELED_PAYMENT = 'pragma_pay_canceled_payment';

    public function __construct(
        private readonly EventManager $eventManager,
        private readonly Transaction $transaction,
        private readonly OrderPaymentResolverInterface $orderPaymentResolver,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * @throws CommandException
     * @throws LocalizedException
     */
    public function execute(string $paymentId, string $orderIncrementUuid): void
    {
        $payment = $this->orderPaymentResolver->execute($paymentId, $orderIncrementUuid);
        if ($payment === null) {
            throw new CommandException(__('Payment does not exist'));
        }

        $order = $payment->getOrder();
        if ($order->canCancel()) {
            $this->closeTransactions($order->getEntityId(), $payment->getEntityId());
            $order->cancel();
            $this->orderRepository->save($order);
            $eventData = ['order' => $order, 'payment' => $payment];
            $this->eventManager->dispatch(self::PRAGMA_PAY_CANCELED_PAYMENT, $eventData);
        }
    }

    private function closeTransactions(string $orderId, string $paymentId): void
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_id', $orderId)
            ->addFilter('payment_id', $paymentId)
            ->create();

        /** @var TransactionModel[]|TransactionInterface[] $transactions */
        $transactions = $this->transactionRepository->getList($searchCriteria)->getItems();
        foreach ($transactions as $transaction) {
            $transaction->setIsClosed(1);
            $this->transaction->addObject($transaction);
        }
        $this->transaction->save();
    }
}
