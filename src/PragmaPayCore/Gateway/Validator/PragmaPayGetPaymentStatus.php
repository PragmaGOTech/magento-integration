<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Validator;

use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;
use Pragma\PragmaPayCore\Api\PragmaPaymentStatus;

class PragmaPayGetPaymentStatus extends AbstractPragmaPayValidator
{
    public function isSuccessfulTransaction(array $response): bool
    {
        if (!isset($response[PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD])) {
            return false;
        }
        $status = null;
        foreach ($response[PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEMS_FIELD] as $item) {
            $status = $item[PragmaConnectionConfigProviderInterface::API_RESPONSE_ITEM_STATUS_FIELD] ?? null;
        }
        return $status === PragmaPaymentStatus::STATUS_FINANCED;
    }
}
