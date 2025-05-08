<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Service;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Pragma\PragmaPayCore\Api\OrderPaymentResolverInterface;

class OrderPaymentResolver implements OrderPaymentResolverInterface
{
    public function __construct(
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly OrderPaymentRepositoryInterface $paymentRepository,
        private readonly GenerateUuid5 $generateUuid5
    ) {
    }

    public function execute(string $pragmaPaymentId, string $orderIncrementUuid): ?Payment
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('txn_id', $pragmaPaymentId)->create();
        $transactionList = $this->transactionRepository->getList($searchCriteria)->getItems();
        reset($transactionList);
        /** @var TransactionInterface | false $transaction */
        $transaction = current($transactionList);

        if ($transaction === false) {
            return null;
        }
        /** @var Payment $payment */
        $payment = $this->paymentRepository->get($transaction->getPaymentId());
        if ($this->generateUuid5->execute((string)$payment->getOrder()?->getIncrementId()) !== $orderIncrementUuid) {
            return null;
        }

        $payment->setData('is_active', !$transaction->getIsClosed());
        $payment->getOrder()->setData(OrderInterface::PAYMENT, $payment);

        return $payment;
    }
}
