<?php
declare(strict_types=1);

namespace Pragma\PragmaPayWebApi\Api;

interface NotifyOrderProcessorInterface
{
    public function execute(string $status, string $pragmaPaymentId, int $totalAmount, ?string $orderIncrementUuid): void;
}
