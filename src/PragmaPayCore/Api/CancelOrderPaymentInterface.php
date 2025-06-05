<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

use Magento\Payment\Gateway\Command\CommandException;

interface CancelOrderPaymentInterface
{
    /**
     * Cancel order payment
     *
     * @param string $paymentId
     * @param string $orderIncrementUuid
     * @throws CommandException
     */
    public function execute(string $paymentId, string $orderIncrementUuid): void;
}
