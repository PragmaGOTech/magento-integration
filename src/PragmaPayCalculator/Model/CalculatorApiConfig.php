<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCalculator\Model;

use Pragma\PragmaPayCore\Api\PragmaPayCartConfigProviderInterface;
use Pragma\PragmaPayCore\Client\RequestManager;

class CalculatorApiConfig
{
    public function __construct(
        private readonly RequestManager $requestManager,
    ) {
    }

    public function prepareAmount(?float $amount): ?int
    {
        if (
            $amount === null
            || $amount < PragmaPayCartConfigProviderInterface::MINIMUM_AMOUNT
            || $amount > PragmaPayCartConfigProviderInterface::MAXIMUM_AMOUNT
        ) {
            return null;
        }

        return $this->requestManager->formatAmount($amount);
    }
}
