<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

use Magento\Payment\Gateway\Command\CommandException;

interface AcceptOrderPaymentInterface
{
    public function execute(string $paymentId, float $amount, string $orderIncrementUuid): void;
}
