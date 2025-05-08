<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Api;

use Magento\Sales\Model\Order\Payment;

interface OrderPaymentResolverInterface
{
    public function execute(string $pragmaPaymentId, string $orderIncrementUuid): ?Payment;
}
