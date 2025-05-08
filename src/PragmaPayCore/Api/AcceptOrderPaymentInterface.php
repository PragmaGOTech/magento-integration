<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

use Magento\Payment\Gateway\Command\CommandException;

interface AcceptOrderPaymentInterface
{
    /**
     * Accept order payment by capture payment, generate invoice and send email invoice email to customer
     *
     * @param string $paymentId
     * @param float $amount
     * @param string $orderIncrementUuid
     * @throws CommandException
     */
    public function execute(string $paymentId, float $amount, string $orderIncrementUuid): void;
}
