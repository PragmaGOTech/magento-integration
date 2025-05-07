<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\AvailabilityChecker;

use Magento\Quote\Model\Quote;

interface AvailabilityCheckerInterface
{
    public function execute(Quote $quote): bool;
}
