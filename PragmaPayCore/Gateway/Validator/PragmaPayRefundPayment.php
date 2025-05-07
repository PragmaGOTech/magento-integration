<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Validator;

class PragmaPayRefundPayment extends AbstractPragmaPayValidator
{
    public function isSuccessfulTransaction(array $response): bool
    {
        return array_key_exists('bankAccount', $response)
            && array_key_exists('id', $response)
            && array_key_exists('link', $response)
            && array_key_exists('validTill', $response);
    }
}
