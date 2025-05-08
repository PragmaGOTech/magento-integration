<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Validator;

use Pragma\PragmaPayCore\Api\PragmaConnectionConfigProviderInterface;

class PragmaPayCreatePayment extends AbstractPragmaPayValidator
{
    public function isSuccessfulTransaction(array $response): bool
    {
        return array_key_exists(PragmaConnectionConfigProviderInterface::API_RESPONSE_REDIRECT_URL_FIELD, $response)
            && array_key_exists(PragmaConnectionConfigProviderInterface::API_RESPONSE_PAYMENT_ID_FIELD, $response);
    }
}
