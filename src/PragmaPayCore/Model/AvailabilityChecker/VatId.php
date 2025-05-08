<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\AvailabilityChecker;

use Magento\Quote\Model\Quote;

class VatId implements AvailabilityCheckerInterface
{
    public function execute(Quote $quote): bool
    {
        return !empty($quote->getBillingAddress()->getVatId());
    }
}
