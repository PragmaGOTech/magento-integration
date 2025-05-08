<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\AvailabilityChecker;

use Magento\Quote\Model\Quote;

class Currency implements AvailabilityCheckerInterface
{
    public const ALLOWED_CURRENCIES = ['PLN'];

    public function execute(Quote $quote): bool
    {
        return in_array($quote->getStore()->getCurrentCurrencyCode(), self::ALLOWED_CURRENCIES);
    }
}
