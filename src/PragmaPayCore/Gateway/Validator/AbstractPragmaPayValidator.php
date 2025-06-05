<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Gateway\Validator;

use InvalidArgumentException;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

abstract class AbstractPragmaPayValidator extends AbstractValidator
{
    public function validate(array $validationSubject): ResultInterface
    {
        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            throw new InvalidArgumentException('Response does not exist');
        }
        $response = $validationSubject['response'];
        if ($this->isSuccessfulTransaction($response)) {
            return $this->createResult(true);
        }

        return $this->createResult(false, [__('PragmaPay rejected the transaction.')]);
    }

    public function isSuccessfulTransaction(array $response): bool
    {
        return false;
    }
}
