<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Service;

use Magento\Payment\Gateway\Command\CommandException;
use Pragma\PragmaPayCore\Api\AcceptOrderPaymentInterface;
use Pragma\PragmaPayCore\Api\CancelOrderPaymentInterface;
use Pragma\PragmaPayCore\Api\PragmaPaymentStatus;
use Pragma\PragmaPayWebApi\Api\NotifyOrderProcessorInterface;

class NotifyOrderProcessor implements NotifyOrderProcessorInterface
{
    public function __construct(
        private readonly AcceptOrderPaymentInterface $acceptOrderPayment,
        private readonly CancelOrderPaymentInterface $cancelOrderPayment,
    ) {
    }

    /**
     * @throws CommandException
     */
    public function execute(string $status, string $pragmaPaymentId, int $totalAmount, ?string $orderIncrementUuid): void
    {
        $totalAmount = (float)($totalAmount / 100);
        switch ($status) {
            case PragmaPaymentStatus::STATUS_FINANCED:
                $this->acceptOrderPayment->execute($pragmaPaymentId, $totalAmount, $orderIncrementUuid);
                break;
            case PragmaPaymentStatus::STATUS_CANCELED:
            case PragmaPaymentStatus::STATUS_REJECTED:
                $this->cancelOrderPayment->execute($pragmaPaymentId, $orderIncrementUuid);
                break;
            case PragmaPaymentStatus::STATUS_NEW:
            case PragmaPaymentStatus::STATUS_WAITING:
                break;
            default:
                throw new CommandException(__('Unknown Action'));
        }
    }
}
