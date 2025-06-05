<?php
declare(strict_types=1);

namespace Pragma\PragmaPayCore\Model\AvailabilityChecker;

use Magento\Quote\Model\Quote;

class CompositeAvailabilityChecker implements AvailabilityCheckerInterface
{
    public function __construct(private readonly array $availabilityCheckers)
    {
    }

    public function execute(Quote $quote): bool
    {
        foreach ($this->availabilityCheckers as $availabilityChecker) {
            if (!$availabilityChecker->execute($quote)) {
                return false;
            }
        }

        return true;
    }
}
